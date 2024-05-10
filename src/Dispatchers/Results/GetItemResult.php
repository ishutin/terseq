<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results;

use Aws\DynamoDb\Marshaler;
use Terseq\Builders\Casters\Caster;

final readonly class GetItemResult
{
    public function __construct(
        protected array $metadata,
        protected ?array $item = null,
        protected ?array $consumedCapacity = null,
    ) {
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getItem(): ?array
    {
        return $this->item;
    }

    public function getConsumedCapacity(): ?array
    {
        return $this->consumedCapacity;
    }

    public static function create(
        array $result,
        Marshaler $marshaler = new Marshaler(),
        Caster $caster = new Caster(),
    ): self {
        $item = null;

        if (isset($result['Item'])) {
            $item = $caster->castItem($marshaler->unmarshalItem($result['Item']));
        }

        return new self(
            metadata: $result['@metadata'],
            item: $item,
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
        );
    }
}
