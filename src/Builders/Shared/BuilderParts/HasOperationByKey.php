<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use Terseq\Builders\Exceptions\BuilderException;

trait HasOperationByKey
{
    protected mixed $pkValue = null;
    protected mixed $skValue = null;

    public function composite(mixed $pkValue, mixed $skValue = null): static
    {
        return (clone $this)->pk($pkValue)->sk($skValue ?? $pkValue);
    }

    public function pk(mixed $value): static
    {
        $clone = clone $this;

        $clone->pkValue = $value;

        return $clone;
    }

    public function sk(mixed $value): static
    {
        $clone = clone $this;

        $clone->skValue = $value;

        return $clone;
    }

    public function appendKey(array $config): array
    {
        if ($this->table === null) {
            throw new BuilderException('Table is required');
        }

        if (!$this->pkValue && !$this->skValue) {
            throw new BuilderException('Partition key or sort key are required');
        }

        if ($this->pkValue) {
            $config['Key'] = [
                $this->table->getKeysFromMemory()->partitionKey => $this->marshaler->marshalValue($this->pkValue),
            ];
        }

        if ($this->skValue) {
            $config['Key'][$this->table->getKeysFromMemory()->sortKey] = $this->marshaler->marshalValue($this->skValue);
        }

        return $config;
    }
}
