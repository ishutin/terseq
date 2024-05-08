<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions;

use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;

class SortKeyCondition
{
    protected ?string $attribute = null;
    protected array $values = [];
    protected ?ComparisonOperator $operator = null;

    public function getQueryData(): array
    {
        return [
            $this->attribute,
            $this->values,
            $this->operator,
        ];
    }

    public function equal(mixed $value, ?string $attribute = null): static
    {
        return (clone $this)->createSimple($value, ComparisonOperator::EQ, $attribute);
    }

    public function lessThan(mixed $value, ?string $attribute = null): static
    {
        return (clone $this)->createSimple($value, ComparisonOperator::LT, $attribute);
    }

    public function lessOrEqual(mixed $value, ?string $attribute = null): static
    {
        return (clone $this)->createSimple($value, ComparisonOperator::LE, $attribute);
    }

    public function greaterThan(mixed $value, ?string $attribute = null): static
    {
        return (clone $this)->createSimple($value, ComparisonOperator::GT, $attribute);
    }

    public function greaterOrEqual(mixed $value, ?string $attribute = null): static
    {
        return (clone $this)->createSimple($value, ComparisonOperator::GE, $attribute);
    }

    public function between(
        mixed $from,
        mixed $to,
        ?string $attribute = null,
    ): static {
        $clone = clone $this;

        $clone->attribute = $attribute;
        $clone->values = [$from, $to];
        $clone->operator = ComparisonOperator::BETWEEN;

        return $clone;
    }

    public function beginsWith(mixed $value, ?string $attribute = null): static
    {
        return (clone $this)->createSimple($value, ComparisonOperator::BEGINS_WITH, $attribute);
    }

    protected function createSimple(mixed $value, ComparisonOperator $operator, ?string $attribute = null): static
    {
        $this->attribute = $attribute;
        $this->values = [$value];
        $this->operator = $operator;

        return $this;
    }
}
