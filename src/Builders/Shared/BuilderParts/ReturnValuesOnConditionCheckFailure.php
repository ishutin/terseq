<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use Terseq\Builders\Shared\Enums;

trait ReturnValuesOnConditionCheckFailure
{
    protected ?Enums\ReturnValues $returnValuesOnConditionCheckFailure = null;

    public function returnValuesOnConditionCheckFailure(Enums\ReturnValues $values): static
    {
        $clone = clone $this;
        $clone->returnValuesOnConditionCheckFailure = $values;

        return $clone;
    }

    protected function appendReturnValuesOnConditionCheckFailure(array $config): array
    {
        if ($this->returnValuesOnConditionCheckFailure) {
            $config['ReturnValuesOnConditionCheckFailure'] = $this->returnValuesOnConditionCheckFailure->value;
        }

        return $config;
    }
}
