<?php

declare(strict_types=1);

namespace Terseq\Facades\Results;

use Aws\DynamoDb\Marshaler;
use Terseq\Facades\Results\Helpers\Transact\ConvertResponses;

readonly class TransactGetItemsResult
{
    use ConvertResponses;

    public function __construct(
        public ?array $consumedCapacity = null,
        public ?array $responses = null,
    ) {
    }

    public static function create(
        array $result,
        Marshaler $marshaler,
    ): static {
        $result = static::convertResponses($result, $marshaler);

        return new static(
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
            responses: $result['Responses'] ?? null,
        );
    }
}
