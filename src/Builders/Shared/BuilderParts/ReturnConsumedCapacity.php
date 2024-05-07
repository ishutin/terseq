<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use Terseq\Builders\Shared\Enums;

trait ReturnConsumedCapacity
{
    protected ?Enums\ReturnConsumedCapacity $returnConsumedCapacity = null;

    public function returnConsumedCapacity(Enums\ReturnConsumedCapacity $returnConsumedCapacity): static
    {
        $clone = clone $this;
        $clone->returnConsumedCapacity = $returnConsumedCapacity;

        return $clone;
    }

    protected function appendReturnConsumedCapacity(array $config): array
    {
        if ($this->returnConsumedCapacity) {
            $config['ReturnConsumedCapacity'] = $this->returnConsumedCapacity->value;
        }

        return $config;
    }
}
