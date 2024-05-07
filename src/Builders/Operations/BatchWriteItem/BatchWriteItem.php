<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\BatchWriteItem;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics;
use Terseq\Contracts\Builder\TableInterface;

class BatchWriteItem extends Builder
{
    use ReturnConsumedCapacity;
    use ReturnItemCollectionMetrics;

    protected array $requestItems = [];

    public function deleteByCompositeKey(
        mixed $pkValue,
        mixed $skValue,
        ?string $pkAttribute = null,
        ?string $skAttribute = null,
        TableInterface|string|array|null $table = null
    ): static {
        $clone = clone $this;

        $table = $clone->createOrGetTable($table);

        if ($pkAttribute === null) {
            $pkAttribute = $table->getPartitionKey();
        }

        if ($skAttribute === null) {
            $skAttribute = $table->getSortKey();
        }

        $clone->requestItems[$table->getTableName()][] = [
            'DeleteRequest' => [
                'Key' => [
                    $pkAttribute => $clone->marshaler->marshalValue($pkValue),
                    $skAttribute => $clone->marshaler->marshalValue($skValue),
                ],
            ],
        ];

        return $clone;
    }

    public function deleteByKey(
        mixed $value,
        ?string $attribute = null,
        TableInterface|string|array|null $table = null,
    ): static {
        $clone = clone $this;

        $table = $clone->createOrGetTable($table);

        if ($attribute === null) {
            $attribute = $table->getPartitionKey();
        }

        $clone->requestItems[$table->getTableName()][] = [
            'DeleteRequest' => [
                'Key' => [
                    $attribute => $clone->marshaler->marshalValue($value),
                ],
            ],
        ];

        return $clone;
    }

    public function put(array $item, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;
        $table = $clone->createOrGetTable($table);
        $clone->requestItems[$table->getTableName()][] = [
            'PutRequest' => [
                'Item' => $clone->marshaler->marshalItem($item),
            ],
        ];

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendReturnConsumedCapacity($config);
        $config = $this->appendReturnItemCollectionMetrics($config);

        $config['RequestItems'] = $this->requestItems;

        return $config;
    }
}
