<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions\Condition;

abstract class Condition
{
    protected bool $isNotCondition = false;
    protected string $type = 'AND';

    abstract public function prepare(bool $isFirst): string;

    protected function getStartCondition(bool $isFirst): string
    {
        $result = $isFirst ? '' : sprintf(' %s ', $this->type);

        if ($this->isNotCondition) {
            $result .= 'NOT ';
        }

        return $result;
    }
}
