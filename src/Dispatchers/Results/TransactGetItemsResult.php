<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results;

use Aws\DynamoDb\Marshaler;
use Terseq\Dispatchers\Results\Helpers\Transact\ConvertResponses;

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
    ): self {
        $result = static::convertResponses($result, $marshaler);

        return new self(
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
            responses: $result['Responses'] ?? null,
        );
    }
}
