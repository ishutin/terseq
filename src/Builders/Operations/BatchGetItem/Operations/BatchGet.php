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

        if ($pkAttribute === null) {
            $pkAttribute = $clone->table->getPartitionKey();
        }

        if ($skAttribute === null) {
            $skAttribute = $clone->table->getSortKey();
        }

        $clone->keys[] = [
            $pkAttribute => $clone->marshaler->marshalValue($pkValue),
            $skAttribute => $clone->marshaler->marshalValue($skValue ?? $pkValue),
        ];

        return $clone;
    }

    public function pk(
        mixed $value,
        mixed $attribute = null,
    ): static {
        $clone = clone $this;

        $attribute = $attribute ?? $clone->table->getPartitionKey();

        $clone->keys[] = [
            $attribute => $clone->marshaler->marshalValue($value),
        ];

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig(withoutTable: true);

        $config = $this->appendConsistentRead($config);
        $config = $this->appendProjectionExpression($config);
        $config = $this->appendAttributes($config, withValues: false);

        $config['Keys'] = $this->keys;

        return $config;
    }
}
