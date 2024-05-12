<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results;

use Aws\DynamoDb\Marshaler;

use function array_map;

readonly class BatchWriteItemResult
{
    public function __construct(
        protected ?array $consumedCapacity = null,
        protected ?array $itemCollectionMetrics = null,
        protected ?array $unprocessedItems = null,
    ) {
    }

    public function getConsumedCapacity(): ?array
    {
        return $this->consumedCapacity;
    }

    public function getItemCollectionMetrics(): ?array
    {
        return $this->itemCollectionMetrics;
    }

    public function getUnprocessedItems(): ?array
    {
        return $this->unprocessedItems;
    }

    public static function create(array $result, Marshaler $marshaler = new Marshaler()): self
    {
        $result = static::convertUnprocessedItems($result, $marshaler);

        return new self(
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
            itemCollectionMetrics: $result['ItemCollectionMetrics'] ?? null,
            unprocessedItems: $result['UnprocessedItems'] ?? null,
        );
    }

    protected static function convertUnprocessedItems(array $result, Marshaler $marshaler): array
    {
        if (isset($result['UnprocessedItems'])) {
            foreach ($result['UnprocessedItems'] as $key => $value) {
                $result['UnprocessedItems'][$key] = array_map(
                    static fn (array $item) => array_map(
                        static fn (array $request) => array_map(
                            static fn (array $key) => $marshaler->unmarshalItem($key),
                            $request,
                        ),
                        $item,
                    ),
                    $value,
                );
            }
        }

        return $result;
    }
}
