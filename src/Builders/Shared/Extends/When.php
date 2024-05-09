<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\Extends;

use Closure;

trait When
{
    public function when(bool $condition, Closure $callback): static
    {
        if ($condition) {
            $clone = clone $this;
            return $callback($clone);
        }

        return $this;
    }
}
