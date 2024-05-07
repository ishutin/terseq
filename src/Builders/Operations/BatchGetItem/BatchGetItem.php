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
     * @var BatchGet[][] $requestItems
     */
    protected array $requestItems = [];

    public function get(Closure $closure, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;
        $table = $table ? $clone->createOrGetTable($table) : null;

        $get = new BatchGet(table: $table, marshaler: $clone->marshaler);

        $get = $closure($get);

        $tableName = $clone->getTableName();

        if (isset($clone->requestItems[$tableName])) {
            $clone->requestItems[$tableName] = [];
        }

        $clone->requestItems[$tableName][] = $get;

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig();
        $config = $this->appendReturnConsumedCapacity($config);

        foreach ($this->requestItems as $tableName => $items) {
            if ($tableName === static::TEMPORARY_TABLE_NAME) {
                $tableName = $this->getTableName();
            }

            $config['RequestItems'][$tableName] = [];

            foreach ($items as $item) {
                $config['RequestItems'][$tableName] = array_merge(
                    $config['RequestItems'][$tableName],
                    $item->table($this->table)->getQuery(),
                );
            }
        }

        return $config;
    }
}
