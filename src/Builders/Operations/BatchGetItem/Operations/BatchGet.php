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

    protected array $keys = [];

    public function composite(
        mixed $pkValue,
        mixed $skValue = null,
        mixed $pkAttribute = null,
        mixed $skAttribute = null,
    ): static {
        $clone = clone $this;

        $pkAttribute = $pkAttribute ?? $clone->getPartitionKey();
        $skAttribute = $skAttribute ?? $clone->getSortKey();

        $clone->keys[] = [
            $pkAttribute => $pkValue,
            $skAttribute => $skValue ?? $pkValue,
        ];

        return $clone;
    }

    public function pk(
        mixed $value,
        mixed $attribute = null,
    ): static {
        $clone = clone $this;

        $attribute = $attribute ?? $clone->getPartitionKey();

        $clone->keys[] = [
            $attribute => $value,
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

        foreach ($this->keys as $index => $attributes) {
            foreach ($attributes as $attribute => $value) {
                if ($attribute === static::TEMPORARY_PK_NAME) {
                    $attribute = $this->getPartitionKey();
                }

                if ($attribute === static::TEMPORARY_SK_NAME) {
                    $attribute = $this->getSortKey();
                }

                $config['Keys'][$index][$attribute] = $this->marshaler->marshalValue($value);
            }
        }

        return $config;
    }
}
