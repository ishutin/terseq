<?php

declare(strict_types=1);

namespace Terseq\Contracts\Dispatchers\Casters;

interface CasterInterface
{
    public function cast(mixed $value): mixed;
}
