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

    public function get(TableInterface|string|array $table, Closure $closure): static
    {
        $clone = clone $this;
        $table = $this->createTable($table);

        $get = new BatchGet(table: $table, marshaler: $clone->marshaler);

        $get = $closure($get);

        if (isset($clone->requestItems[$table->getTableName()])) {
            $clone->requestItems[$table->getTableName()] = [];
        }

        $clone->requestItems[$table->getTableName()][] = $get;

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig();
        $config = $this->appendReturnConsumedCapacity($config);

        foreach ($this->requestItems as $tableName => $items) {
            $config['RequestItems'][$tableName] = [];

            foreach ($items as $item) {
                $config['RequestItems'][$tableName] = array_merge(
                    $config['RequestItems'][$tableName],
                    $item->getQuery(),
                );
            }
        }

        return $config;
    }
}
