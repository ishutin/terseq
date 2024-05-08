<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\BatchGetItem;

use Closure;
use Terseq\Builders\Operations\BatchGetItem\Operations\BatchGet;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Contracts\Builder\TableInterface;

use function array_merge;

class BatchGetItem extends Builder
{
    use ReturnConsumedCapacity;

    /**
     * @var BatchGet[] $requestItems
     */
    protected array $requestItems = [];
    protected array $requestItemsKeys = [];

    public function get(Closure $closure, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;
        $table = $table ? $clone->createOrGetTable($table) : null;

        $clone->requestItems[] = $closure(new BatchGet(table: $table, marshaler: $clone->marshaler));
        $clone->requestItemsKeys[] = $table?->getTableName();

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig(withoutTable: true);
        $config = $this->appendReturnConsumedCapacity($config);

        foreach ($this->requestItems as $keyIndex => $item) {
            $key = $this->requestItemsKeys[$keyIndex] ?? $this->table->getTableName();

            $config['RequestItems'][$key] = array_merge(
                $config['RequestItems'][$key] ?? [],
                $item->table($this->table)->getQuery(),
            );
        }

        return $config;
    }
}
