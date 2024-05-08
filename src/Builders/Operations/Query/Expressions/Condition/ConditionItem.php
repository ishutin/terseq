<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions\Condition;

use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Shared\Extends\RenderCondition;

class ConditionItem extends Condition
{
    use RenderCondition;

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
            $this->renderCondition($this->operator, $this->values, $this->attribute),
        );
    }
}
