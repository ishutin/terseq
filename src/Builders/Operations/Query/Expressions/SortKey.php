<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions;

use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Operations\Query\Expressions\Condition\ConditionItem;

class SortKey extends Expression
{
    public function equal(mixed $attribute, mixed $value = null): static
    {
        $this->modifyAttributeAndValue($attribute, $value);

        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::EQ,
        );
    }

    public function lessThan(string $attribute, mixed $value): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::LT,
        );
    }

    public function lessOrEqual(string $attribute, mixed $value): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::LE,
        );
    }

    public function greaterThan(string $attribute, mixed $value): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::GT,
        );
    }

    public function greaterOrEqual(string $attribute, mixed $value): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::GE,
        );
    }

    public function between(
        string $attribute,
        mixed $from,
        mixed $to,
    ): static {
        $this->modifyAttributeAndValue($attribute, $value);

        $this->conditions = [
            new ConditionItem(
                attribute: $this->createAttribute($attribute),
                values: [
                    $this->query->valuesStorage->createValue($attribute, $from),
                    $this->query->valuesStorage->createValue($attribute, $to),
                ],
                operator: ComparisonOperator::BETWEEN,
            ),
        ];

        return $this;
    }

    public function beginsWith(string $attribute, string $value): static
    {
        $this->modifyAttributeAndValue($attribute, $value);

        $this->conditions = [
            new ConditionItem(
                attribute: $this->createAttribute($attribute),
                values: [
                    $this->query->valuesStorage->createValue($attribute, $value),
                ],
                operator: ComparisonOperator::BEGINS_WITH,
            ),
        ];

        return $this;
    }

    protected function getDefaultAttributeName(): ?string
    {
        return $this->query->table->getSortKey();
    }
}
