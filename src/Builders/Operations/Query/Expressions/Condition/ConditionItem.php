<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions\Condition;

use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;

class ConditionItem extends Condition
{
    public function __construct(
        protected readonly string $attribute,
        protected readonly array $values,
        protected readonly ComparisonOperator $operator,
        protected string $type = 'AND',
        protected bool $isNotCondition = false,
    ) {
    }

    public function prepare(bool $isFirst): string
    {
        return sprintf(
            '%s%s',
            $this->getStartCondition($isFirst),
            $this->getBody(),
        );
    }

    protected function getBody(): string
    {
        switch ($this->operator) {
            case ComparisonOperator::EQ:
            case ComparisonOperator::NE:
            case ComparisonOperator::LE:
            case ComparisonOperator::LT:
            case ComparisonOperator::GE:
            case ComparisonOperator::GT:
                if (empty($this->values)) {
                    throw new BuilderException('Values cannot be empty');
                }

                return sprintf('%s %s %s', $this->attribute, $this->operator->value, $this->values[0]);
            case ComparisonOperator::BETWEEN:
                if (count($this->values) !== 2) {
                    throw new BuilderException('Incorrect values count');
                }

                return sprintf('%s BETWEEN %s AND %s', $this->attribute, $this->values[0], $this->values[1]);
            case ComparisonOperator::ATTRIBUTE_NOT_EXISTS:
            case ComparisonOperator::ATTRIBUTE_EXISTS:
                return sprintf('%s(%s)', $this->operator->value, $this->attribute);
            case ComparisonOperator::ATTRIBUTE_TYPE:
            case ComparisonOperator::BEGINS_WITH:
            case ComparisonOperator::CONTAINS:
                if (empty($this->values)) {
                    throw new BuilderException('Values cannot be empty');
                }

                return sprintf('%s(%s, %s)', $this->operator->value, $this->attribute, $this->values[0]);
            case ComparisonOperator::IN:
                if (empty($this->values)) {
                    throw new BuilderException('Values cannot be empty');
                }

                return sprintf(
                    '%s %s (%s)',
                    $this->attribute,
                    $this->operator->value,
                    implode(', ', $this->values),
                );
        }

        return throw new BuilderException('Incorrect operator');
    }
}
