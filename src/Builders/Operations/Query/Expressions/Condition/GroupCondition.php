<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions\Condition;

class GroupCondition extends Condition
{
    /**
     * @param Condition[] $conditions
     */
    public function __construct(
        protected readonly array $conditions = [],
        protected string $type = 'AND',
    ) {
    }

    public function prepare(bool $isFirst): string
    {
        $nestedConditions = '';

        foreach ($this->conditions as $index => $condition) {
            $nestedConditions .= $condition->prepare($index === 0);
        }

        return sprintf(
            '%s(%s)',
            $this->getStartCondition($isFirst),
            $nestedConditions,
        );
    }

}
