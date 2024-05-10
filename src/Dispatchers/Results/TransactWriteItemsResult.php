<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results;

use Aws\DynamoDb\Marshaler;
use Terseq\Dispatchers\Results\Helpers\Transact\ConvertMultiplyItemCollectionMetrics;

readonly class TransactWriteItemsResult
{
    use ConvertMultiplyItemCollectionMetrics;

    public function __construct(
        public ?array $consumedCapacity = null,
        public ?array $itemCollectionMetrics = null,
    ) {
    }

    public static function create(array $result, Marshaler $marshaler = new Marshaler()): self
    {
        $result = static::convertMultiplyItemCollectionMetric($result, $marshaler);

        return new self(
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
            itemCollectionMetrics: $result['ItemCollectionMetrics'] ?? null,
        );
    }
}
