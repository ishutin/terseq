<?php

declare(strict_types=1);

namespace Terseq\Facades\Results;

use Aws\DynamoDb\Marshaler;
use Generator;
use loophp\collection\Contract\Collection;
use Terseq\Builders\Casters\Caster;

use function array_map;
use function is_array;

final readonly class QueryResult
{
    public function __construct(
        protected int $count,
        protected int $scannedCount,
        protected array $metadata,
        protected ?Collection $items = null,
        protected ?array $lastEvaluatedKey = null,
        protected ?array $consumedCapacity = null,
    ) {
    }

    public function getItems(): ?Collection
    {
        return $this->items;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getScannedCount(): int
    {
        return $this->scannedCount;
    }

    public function getLastEvaluatedKey(): ?array
    {
        return $this->lastEvaluatedKey;
    }

    public function getConsumedCapacity(): ?array
    {
        return $this->consumedCapacity;
    }


    public static function create(
        array $result,
        Marshaler $marshaler = new Marshaler(),
        ?Caster $caster = new Caster(),
    ): self {
        $items = null;

        $lastEvaluatedKey = $result['LastEvaluatedKey'] ?? null;

        if (!empty($result['Items'])) {
            $itemsFn = static fn (): Generator => yield from array_map(
                static fn (array $item): array => $caster->castItem($marshaler->unmarshalItem($item)),
                $result['Items'],
            );

            $items = \loophp\collection\Collection::fromGenerator($itemsFn());
        }

        if (is_array($lastEvaluatedKey)) {
            $lastEvaluatedKey = $marshaler->unmarshalItem($lastEvaluatedKey);
        }

        return new self(
            count: $result['Count'],
            scannedCount: $result['ScannedCount'],
            metadata: $result['@metadata'],
            items: $items,
            lastEvaluatedKey: $lastEvaluatedKey,
            consumedCapacity: $result['ConsumedCapacity'] ?? [],
        );
    }
}
