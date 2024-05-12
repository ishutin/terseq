<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results;

use Aws\DynamoDb\Marshaler;
use Terseq\Dispatchers\Results\Helpers\Batch\ConvertResponses;

use function array_map;

readonly class BatchGetItemResult
{
    use ConvertResponses;

    public function __construct(
        protected ?array $responses,
        protected ?array $unprocessedKeys,
        protected ?array $consumedCapacity,
    ) {
    }

    public function getResponses(): ?array
    {
        return $this->responses;
    }

    public function getUnprocessedKeys(): ?array
    {
        return $this->unprocessedKeys;
    }

    public function getConsumedCapacity(): ?array
    {
        return $this->consumedCapacity;
    }

    public static function create(array $result, Marshaler $marshaler = new Marshaler()): self
    {
        $result = self::convertResponses($result, $marshaler);
        $result = self::convertUnprocessedKeys($result, $marshaler);

        return new self(
            responses: $result['Responses'] ?? null,
            unprocessedKeys: $result['UnprocessedKeys'] ?? null,
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
        );
    }

    protected static function convertUnprocessedKeys(array $result, Marshaler $marshaler): array
    {
        if (isset($result['UnprocessedKeys'])) {
            foreach ($result['UnprocessedKeys'] as $key => $value) {
                $result['UnprocessedKeys'][$key]['Keys'] = array_map(
                    static fn (array $attribute) => $marshaler->unmarshalItem($attribute),
                    $result['UnprocessedKeys'][$key]['Keys'],
                );
            }
        }

        return $result;
    }
}
