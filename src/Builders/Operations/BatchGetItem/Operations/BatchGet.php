<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\BatchGetItem\Operations;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\ConsistentRead;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\ProjectionExpression;

class BatchGet extends Builder
{
    use ConsistentRead;
    use ProjectionExpression;
    use HasAttributes;
    use AppendAttributes;

    protected array $values = [];
    protected array $keys = [];

    public function composite(
        mixed $pkValue,
        mixed $skValue = null,
        mixed $pkAttribute = null,
        mixed $skAttribute = null,
    ): static {
        $clone = clone $this;

        $clone->values[] = [
            $pkValue,
            $skValue ?? $pkValue,
        ];

        $clone->keys[] = [
            $pkAttribute,
            $skAttribute,
        ];

        return $clone;
    }

    public function pk(
        mixed $value,
        mixed $attribute = null,
    ): static {
        $clone = clone $this;

        $clone->values[] = [
            $value,
        ];

        $clone->keys[] = [
            $attribute,
        ];

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig(withoutTable: true);

        $config = $this->appendConsistentRead($config);
        $config = $this->appendProjectionExpression($config);
        $config = $this->appendAttributes($config, withValues: false);

        $config['Keys'] = [];

        $defaultKeys = $this->table->getKeysFromMemory()->toArray();

        foreach ($this->values as $index => $attributes) {
            foreach ($attributes as $keyIndex => $value) {
                $key = $this->keys[$index][$keyIndex] ?? $defaultKeys[$keyIndex];
                $config['Keys'][$index][$key] = $this->marshaler->marshalValue($value);
            }
        }

        return $config;
    }
}
