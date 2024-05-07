<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions;

use Closure;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Operations\Query\Expressions\Condition\ConditionItem;
use Terseq\Builders\Operations\Query\Expressions\Condition\GroupCondition;

use function array_map;
use function array_merge;

class FilterExpression extends Expression
{
    public function group(Closure $closure, string $type = 'AND'): static
    {
        $where = new FilterExpression($this->query);

        $closure($where);

        $this->attributes = array_merge($this->attributes, $where->getAttributes());

        $this->conditions[] = new GroupCondition(
            conditions: $where->getConditions(),
            type: $type,
        );

        return $this;
    }

    // Move code from 5 first traits to here

    public function equal(string $attribute, mixed $value, string $type = 'AND', bool $not = false): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::EQ,
            $type,
            $not,
        );
    }

    public function lessThan(string $attribute, mixed $value, string $type = 'AND', bool $not = false): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::LT,
            $type,
            $not,
        );
    }

    public function lessOrEqual(string $attribute, mixed $value, string $type = 'AND', bool $not = false): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::LE,
            $type,
            $not,
        );
    }

    public function greaterThan(string $attribute, mixed $value, string $type = 'AND', bool $not = false): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::GT,
            $type,
            $not,
        );
    }

    public function greaterOrEqual(string $attribute, mixed $value, string $type = 'AND', bool $not = false): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::GE,
            $type,
            $not,
        );
    }

    public function between(
        string $attribute,
        mixed $from,
        mixed $to,
        string $type = 'AND',
        bool $not = false,
    ): static {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [
                $this->query->valuesStorage->createValue($attribute, $from),
                $this->query->valuesStorage->createValue($attribute, $to),
            ],
            operator: ComparisonOperator::BETWEEN,
            type: $type,
            isNotCondition: $not,
        );

        return $this;
    }

    public function beginsWith(string $attribute, string $value, string $type = 'AND', bool $not = false): static
    {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [
                $this->query->valuesStorage->createValue($attribute, $value),
            ],
            operator: ComparisonOperator::BEGINS_WITH,
            type: $type,
            isNotCondition: $not,
        );

        return $this;
    }

    public function in(string $attribute, array $in, string $type = 'AND', bool $not = false): static
    {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: array_map(function (mixed $value) use ($attribute) {
                return $this->query->valuesStorage->createValue($attribute, $value);
            }, $in),
            operator: ComparisonOperator::IN,
            type: $type,
            isNotCondition: $not,
        );

        return $this;
    }

    public function attributeExists(string $attribute, string $type = 'AND', bool $not = false): static
    {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [],
            operator: ComparisonOperator::ATTRIBUTE_EXISTS,
            type: $type,
            isNotCondition: $not,
        );

        return $this;
    }

    public function attributeNotExists(string $attribute, string $type = 'AND', bool $not = false): static
    {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [],
            operator: ComparisonOperator::ATTRIBUTE_NOT_EXISTS,
            type: $type,
            isNotCondition: $not,
        );

        return $this;
    }

    public function notEqual(string $attribute, mixed $value, string $type = 'AND', bool $not = false): static
    {
        return $this->simpleCondition(
            $attribute,
            $value,
            ComparisonOperator::NE,
            $type,
            $not,
        );
    }

    public function contains(string $attribute, string $value, string $type = 'AND', bool $not = false): static
    {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [
                $this->query->valuesStorage->createValue($attribute, $value),
            ],
            operator: ComparisonOperator::CONTAINS,
            type: $type,
            isNotCondition: $not,
        );

        return $this;
    }


}
