<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\Extends;

use Terseq\Builders\Casters\Caster;
use Terseq\Contracts\Facades\Casters\CasterInterface;

trait HasCasters
{
    protected ?Caster $caster = null;

    public function addCaster(string $attribute, CasterInterface $caster): static
    {
        $clone = clone $this;
        if ($clone->caster === null) {
            $clone->caster = new Caster();
        }

        $clone->caster = $clone->caster->add($attribute, $caster);

        return $clone;
    }

    public function getCaster(): ?Caster
    {
        return $this->caster;
    }
}
