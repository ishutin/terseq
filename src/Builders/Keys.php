<?php

declare(strict_types=1);

namespace Terseq\Builders;

readonly class Keys
{
    public function __construct(
        public string $partitionKey,
        public ?string $sortKey = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            $this->partitionKey,
            $this->sortKey,
        ];
    }
}
