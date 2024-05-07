<?php

declare(strict_types=1);

namespace Terseq\Facades\Results;

use Aws\DynamoDb\Marshaler;
use Terseq\Facades\Results\Helpers\Transact\ConvertMultiplyItemCollectionMetrics;

readonly class TransactWriteItemsResult
{
    use ConvertMultiplyItemCollectionMetrics;

    public function __construct(
        public ?array $consumedCapacity = null,
        public ?array $itemCollectionMetrics = null,
    ) {
    }

    public static function create(array $result, Marshaler $marshaler = new Marshaler()): static
    {
        $result = static::convertMultiplyItemCollectionMetric($result, $marshaler);

        return new static(
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
            itemCollectionMetrics: $result['ItemCollectionMetrics'] ?? null,
        );
    }
}
