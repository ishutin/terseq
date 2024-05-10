<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics;
use Terseq\Contracts\Builder\TableInterface;

class BatchWriteItem extends Builder
{
    use ReturnConsumedCapacity;
    use ReturnItemCollectionMetrics;

    protected array $putItems = [];
    protected array $putItemsKeys = [];
    protected array $deleteItems = [];
    protected array $deleteItemsKeys = [];
    protected array $deleteTableNames = [];

    public function deleteComposite(
        mixed $pkValue,
        mixed $skValue = null,
        ?string $pkAttribute = null,
        ?string $skAttribute = null,
        TableInterface|string|array|null $table = null,
    ): static {
        $clone = clone $this;

        $clone->deleteItems[] = [
            $pkValue,
            $skValue ?? $pkValue,
        ];

        $clone->deleteItemsKeys[] = [
            $pkAttribute ?? $table?->getKeysFromMemory()->partitionKey,
            $skAttribute ?? $table?->getKeysFromMemory()->sortKey,
        ];

        $clone->deleteTableNames[] = $table?->getTableName();

        return $clone;
    }

    public function delete(
        mixed $value,
        ?string $attribute = null,
        TableInterface|string|array|null $table = null,
    ): static {
        $clone = clone $this;

        $clone->deleteItems[] = [
            $value,
        ];

        $clone->deleteItemsKeys[] = [
            $attribute ?? $table?->getKeys()->partitionKey,
        ];

        $clone->deleteTableNames[] = $table?->getTableName();

        return $clone;
    }

    public function put(array $item, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;
        $table = $table ? $clone->createOrGetTable($table) : null;

        $clone->putItems[] = $item;
        $clone->putItemsKeys[] = $table?->getTableName();

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig(withoutTable: true);

        $config = $this->appendReturnConsumedCapacity($config);
        $config = $this->appendReturnItemCollectionMetrics($config);

        $requestItems = [];

        $itemsCount = 0;

        foreach ($this->putItems as $index => $item) {
            $tableName = $this->putItemsKeys[$index] ?? $this->table->getTableName();
            $requestItems[$tableName][] = [
                'PutRequest' => [
                    'Item' => $this->marshaler->marshalItem($item),
                ],
            ];
            $itemsCount++;
        }

        $defaultKeys = $this->table?->getKeysFromMemory()->toArray() ?? [];

        foreach ($this->deleteItems as $index => $items) {
            $tableName = $this->deleteTableNames[$index] ?? $this->table->getTableName();

            foreach ($items as $itemIndex => $value) {
                $key = $this->deleteItemsKeys[$index][$itemIndex] ?? $defaultKeys[$itemIndex];

                $requestItems[$tableName][] = [
                    'DeleteRequest' => [
                        'Key' => [
                            $key => $this->marshaler->marshalValue($value),
                        ],
                    ],
                ];

                $itemsCount++;
            }
        }

        if ($itemsCount > 25) {
            throw new BuilderException('You can only write up to 25 items at a time');
        }

        $config['RequestItems'] = $requestItems;

        return $config;
    }
}
