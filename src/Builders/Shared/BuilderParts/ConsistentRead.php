<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

trait ConsistentRead
{
    protected ?bool $consistentRead = null;

    public function setConsistentRead(bool $consistentRead): static
    {
        $clone = clone $this;
        $clone->consistentRead = $consistentRead;

        return $clone;
    }

    public function consistentRead(): static
    {
        return (clone $this)->setConsistentRead(true);
    }

    public function notConsistentRead(): static
    {
        return (clone $this)->setConsistentRead(false);
    }

    protected function appendConsistentRead(array $config): array
    {
        if ($this->consistentRead) {
            $config['ConsistentRead'] = $this->consistentRead;
        }

        return $config;
    }
}
