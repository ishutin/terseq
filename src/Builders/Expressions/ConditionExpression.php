<?php

declare(strict_types=1);

namespace Terseq\Builders\Expressions;

use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;

class ConditionExpression extends FilterExpression
{
    public function attributeType(string $attribute, string $value, string $type = 'AND', bool $not = false): static
    {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [
                $this->valuesStorage->createValue($attribute, $value),
            ],
            operator: ComparisonOperator::ATTRIBUTE_TYPE,
            type: $type,
            isNotCondition: $not,
        );

        return $this;
    }

    public function size(
        string $attribute,
        string $operator,
        int $value,
        string $type = 'AND',
        bool $not = false,
    ): static {
        $this->conditions[] = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [
                $this->valuesStorage->createValue($attribute, $value),
            ],
            operator: ComparisonOperator::from($operator),
            type: $type,
            isNotCondition: $not,
            additionalOperator: ComparisonOperator::SIZE,
        );

        return $this;
    }
}
