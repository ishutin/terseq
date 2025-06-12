<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use Closure;
use Terseq\Builders\Expressions;

use function array_merge;

trait ConditionExpression
{
    protected ?Expressions\ConditionExpression $conditionExpression = null;

    /**
     * @param Closure(Expressions\ConditionExpression): Expressions\ConditionExpression $closure
     */
    public function conditionExpression(Closure $closure): static
    {
        $clone = clone $this;

        $clone->conditionExpression = $closure(
            new Expressions\ConditionExpression($clone->getValuesStorage()),
        );
        $clone->attributes = array_merge($clone->attributes, $clone->conditionExpression->getAttributes());

        return $clone;
    }

    protected function appendConditionExpression(array $config): array
    {
        if ($this->conditionExpression) {
            $config['ConditionExpression'] = $this->conditionExpression->prepare();
        }

        return $config;
    }
}
