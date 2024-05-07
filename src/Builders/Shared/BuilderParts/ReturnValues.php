<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use Terseq\Builders\Shared\Enums;

trait ReturnValues
{
    protected ?Enums\ReturnValues $returnValues = null;

    public function returnValues(Enums\ReturnValues $values): static
    {
        $clone = clone $this;
        $clone->returnValues = $values;

        return $clone;
    }

    protected function appendReturnValues(array $config): array
    {
        if ($this->returnValues) {
            $config['ReturnValues'] = $this->returnValues->value;
        }

        return $config;
    }
}
