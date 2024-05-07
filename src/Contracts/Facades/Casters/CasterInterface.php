<?php

declare(strict_types=1);

namespace Terseq\Contracts\Facades\Casters;

interface CasterInterface
{
    public function cast(mixed $value): mixed;
}
