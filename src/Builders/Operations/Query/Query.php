<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query;

use Closure;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\Query\Enums\Select;
use Terseq\Builders\Operations\Query\Expressions\FilterExpression;
use Terseq\Builders\Operations\Query\Expressions\SortKey;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\ConsistentRead;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\ProjectionExpression;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Extends\HasCasters;

use function array_merge;
use function sprintf;

class Query extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use ProjectionExpression;
    use ConsistentRead;
    use ReturnConsumedCapacity;

    use HasCasters;

    protected ?SortKey $skExpression = null;
    protected ?FilterExpression $filterExpression = null;
    protected ?int $limit = null;
    protected ?Select $select = null;
    protected ?string $indexName = null;
    protected ?bool $scanIndexForward = null;
    protected ?array $exclusiveStartKey = null;
    protected ?string $pkAttribute = null;
    protected ?string $pkValue = null;
    protected ?string $skAttribute = null;
    protected ?string $skValue = null;

    public function exclusiveStartKey(array $startKey): static
    {
        $clone = clone $this;
        $clone->exclusiveStartKey = $startKey;

        return $clone;
    }

    public function select(Select $select): static
    {
        $clone = clone $this;
        $clone->select = $select;

        return $clone;
    }

    public function scanIndexForward(): static
    {
        return (clone $this)->setScanIndexForward(true);
    }

    public function notScanIndexForward(): static
    {
        return (clone $this)->setScanIndexForward(false);
    }

    public function setScanIndexForward(?bool $state): static
    {
        $clone = clone $this;
        $clone->scanIndexForward = $state;

        return $clone;
    }

    public function composite(
        mixed $pkValue,
        mixed $skValue = null,
        ?string $pkAttribute = null,
        ?string $skAttribute = null,
    ): static {
        return (clone $this)
            ->pk($pkValue, $pkAttribute)
            ->sk($skValue ?? $pkValue, $skAttribute);
    }

    public function pk(mixed $value, ?string $attribute = null): static
    {
        $clone = clone $this;
        $attribute = $attribute ?? $clone->table->getPartitionKey();
        $attributeName = $clone->createAttribute($attribute);

        $clone->pkAttribute = $attributeName;
        $clone->pkValue = $clone->valuesStorage->createValue($attribute, $value);

        return $clone;
    }

    public function sk(mixed $value, ?string $attribute = null): static
    {
        $clone = clone $this;

        if ($value instanceof Closure) {
            $clone->skExpression = $value(new SortKey($clone));
            $this->attributes = array_merge($clone->getAttributes(), $clone->skExpression->getAttributes());
        } else {
            $attribute = $attribute ?? $clone->table->getSortKey();
            $attributeName = $clone->createAttribute($attribute);

            $clone->skAttribute = $attributeName;
            $clone->skValue = $clone->valuesStorage->createValue($attribute, $value);
        }

        return $clone;
    }

    public function filter(Closure $closure): static
    {
        $clone = clone $this;
        $clone->filterExpression = new FilterExpression($clone);
        $clone->attributes = array_merge($clone->getAttributes(), $clone->filterExpression->getAttributes());

        $closure($clone->filterExpression);

        return $clone;
    }

    public function limit(?int $limit): static
    {
        $clone = clone $this;
        $clone->limit = $limit;

        return $clone;
    }

    public function indexName(string $indexName): static
    {
        $clone = clone $this;
        $clone->indexName = $indexName;

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendProjectionExpression($config);
        $config = $this->appendConsistentRead($config);
        $config = $this->appendReturnConsumedCapacity($config);

        if ($this->indexName) {
            $config['IndexName'] = $this->indexName;
        }

        if (
            $this->pkAttribute === null
            && $this->pkValue === null
        ) {
            throw new BuilderException('Partition key is required for query operation.');
        }

        $config = $this->prepareKeyConditionExpression($config);

        if ($this->filterExpression?->isEmpty() === false) {
            $config['FilterExpression'] = $this->filterExpression->prepare();
        }

        $config = $this->appendAttributes($config);

        if ($this->scanIndexForward !== null) {
            $config['ScanIndexForward'] = $this->scanIndexForward;
        }

        if ($this->limit) {
            $config['Limit'] = $this->limit;
        }

        if ($this->exclusiveStartKey) {
            $config['ExclusiveStartKey'] = $this->marshaler->marshalItem($this->exclusiveStartKey);
        }

        if ($this->select) {
            $config['Select'] = $this->select->value;
        }

        return $config;
    }

    protected function prepareKeyConditionExpression(array $config): array
    {
        if ($this->pkValue) {
            $config['KeyConditionExpression'] = sprintf(
                '%s = %s',
                $this->pkAttribute,
                $this->pkValue,
            );
        }

        if ($this->skExpression?->isEmpty() === false) {
            $config['KeyConditionExpression'] = sprintf(
                '%s AND %s',
                $config['KeyConditionExpression'],
                $this->skExpression->prepare(),
            );
        } elseif ($this->skValue) {
            $config['KeyConditionExpression'] = sprintf(
                '%s AND %s = %s',
                $config['KeyConditionExpression'],
                $this->skAttribute,
                $this->skValue,
            );
        }

        return $config;
    }
}
