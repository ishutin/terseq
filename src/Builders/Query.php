<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Closure;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Expressions\FilterExpression;
use Terseq\Builders\Operations\Query\Enums\Select;
use Terseq\Builders\Operations\Query\SortKeyCondition;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\ConsistentRead;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\ProjectionExpression;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Extends\HasCasters;
use Terseq\Builders\Shared\Extends\RenderCondition;

use function array_merge;
use function implode;
use function sprintf;

class Query extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use ProjectionExpression;
    use ConsistentRead;
    use ReturnConsumedCapacity;

    use HasCasters;
    use RenderCondition;

    protected ?SortKeyCondition $sortKeyCondition = null;
    protected ?FilterExpression $filterExpression = null;
    protected ?int $limit = null;
    protected ?Select $select = null;
    protected ?string $secondaryIndex = null;
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

        $clone->pkAttribute = $attribute;
        $clone->pkValue = $value;

        return $clone;
    }

    public function sk(mixed $value, ?string $attribute = null): static
    {
        $clone = clone $this;

        if ($value instanceof Closure) {
            $clone->sortKeyCondition = $value(
                new SortKeyCondition(),
            );
        } else {
            $clone->skAttribute = $attribute;
            $clone->skValue = $value;
        }

        return $clone;
    }

    public function filter(Closure $closure): static
    {
        $clone = clone $this;
        $clone->filterExpression = new FilterExpression($clone->getValuesStorage());
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

    public function secondaryIndex(string $indexName): static
    {
        $clone = clone $this;
        $clone->secondaryIndex = $indexName;

        return $clone;
    }

    public function getQuery(): array
    {
        $clone = clone $this;
        $config = $clone->createConfig();

        $config = $clone->appendProjectionExpression($config);
        $config = $clone->appendConsistentRead($config);
        $config = $clone->appendReturnConsumedCapacity($config);

        if ($clone->secondaryIndex) {
            $config['IndexName'] = $clone->secondaryIndex;
        }

        if (
            $clone->pkAttribute === null
            && $clone->pkValue === null
        ) {
            throw new BuilderException('Partition key is required for query operation.');
        }

        $config = $clone->prepareKeyConditionExpression($config);

        if ($clone->filterExpression?->isEmpty() === false) {
            $config['FilterExpression'] = $clone->filterExpression->prepare();
        }

        $config = $clone->appendAttributes($config);

        if ($clone->scanIndexForward !== null) {
            $config['ScanIndexForward'] = $clone->scanIndexForward;
        }

        if ($clone->limit) {
            $config['Limit'] = $clone->limit;
        }

        if ($clone->exclusiveStartKey) {
            $config['ExclusiveStartKey'] = $clone->marshaler->marshalItem($clone->exclusiveStartKey);
        }

        if ($clone->select) {
            $config['Select'] = $clone->select->value;
        }

        return $config;
    }

    protected function prepareKeyConditionExpression(array $config): array
    {
        $expression = [];

        if ($this->pkValue) {
            $attribute = $this->getPartitionKey();
            $value = $this->getValuesStorage()->createValue($attribute, $this->pkValue);

            $expression[] = sprintf(
                '%s = %s',
                $this->createAttribute($attribute),
                $value,
            );
        }

        if ($this->skValue) {
            $attribute = $this->getSortKey();
            $value = $this->getValuesStorage()->createValue($attribute, $this->skValue);

            $expression[] = sprintf(
                '%s = %s',
                $this->createAttribute($attribute),
                $value,
            );
        } elseif ($this->sortKeyCondition) {
            $condition = $this->sortKeyCondition;

            [$attribute, $values, $operator] = $condition->getQueryData();

            $attribute = $attribute ?? $this->getSortKey();
            $attributeName = $this->createAttribute($attribute);
            $preparedValues = [];

            foreach ($values as $value) {
                $preparedValues[] = $this->getValuesStorage()->createValue($attribute, $value);
            }

            $expression[] = $this->renderCondition($operator, $preparedValues, $attributeName);
        }

        $config['KeyConditionExpression'] = implode(' AND ', $expression);

        return $config;
    }

    protected function getSortKey(): ?string
    {
        $attribute = $this->table->getKeysFromMemory()->sortKey;

        if (
            $this->secondaryIndex
            && $secondaryKey = ($this->table->getSecondaryIndexMapFromMemory()[$this->secondaryIndex] ?? null)
        ) {
            $attribute = $secondaryKey->sortKey;
        } elseif ($this->skAttribute) {
            $attribute = $this->skAttribute;
        }

        return $attribute;
    }

    protected function getPartitionKey(): string
    {
        $attribute = $this->table->getKeysFromMemory()->partitionKey;

        if (
            $this->secondaryIndex
            && $secondaryKey = ($this->table->getSecondaryIndexMapFromMemory()[$this->secondaryIndex] ?? null)
        ) {
            $attribute = $secondaryKey->partitionKey;
        } elseif ($this->pkAttribute) {
            $attribute = $this->pkAttribute;
        }

        return $attribute;
    }
}
