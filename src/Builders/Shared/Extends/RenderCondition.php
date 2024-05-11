<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\Extends;

use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;

use function sprintf;

trait RenderCondition
{
    /**
     * @throws BuilderException
     */
    public function renderCondition(
        ComparisonOperator $operator,
        array $values,
        string $attribute,
        ?ComparisonOperator $additionalOperator = null,
    ): string {
        switch ($operator) {
            case ComparisonOperator::EQ:
            case ComparisonOperator::NE:
            case ComparisonOperator::LE:
            case ComparisonOperator::LT:
            case ComparisonOperator::GE:
            case ComparisonOperator::GT:
                if (empty($values)) {
                    throw new BuilderException('Values cannot be empty');
                }
                if ($additionalOperator === ComparisonOperator::SIZE) {
                    $attribute = sprintf('size(%s)', $attribute);
                }

                return sprintf('%s %s %s', $attribute, $operator->value, $values[0]);
            case ComparisonOperator::BETWEEN:
                if (count($values) !== 2) {
                    throw new BuilderException('Incorrect values count');
                }

                return sprintf('%s BETWEEN %s AND %s', $attribute, $values[0], $values[1]);
            case ComparisonOperator::ATTRIBUTE_NOT_EXISTS:
            case ComparisonOperator::ATTRIBUTE_EXISTS:
                return sprintf('%s(%s)', $operator->value, $attribute);
            case ComparisonOperator::ATTRIBUTE_TYPE:
            case ComparisonOperator::BEGINS_WITH:
            case ComparisonOperator::CONTAINS:
                if (empty($values)) {
                    throw new BuilderException('Values cannot be empty');
                }

                return sprintf('%s(%s, %s)', $operator->value, $attribute, $values[0]);
            case ComparisonOperator::IN:
                if (empty($values)) {
                    throw new BuilderException('Values cannot be empty');
                }

                return sprintf(
                    '%s %s (%s)',
                    $attribute,
                    $operator->value,
                    implode(', ', $values),
                );
            default:
                throw new BuilderException(sprintf('Operator %s used incorrectly', $operator->value));
        }
    }
}
