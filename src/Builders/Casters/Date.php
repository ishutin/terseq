<?php

declare(strict_types=1);

namespace Terseq\Builders\Casters;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Terseq\Contracts\Facades\Casters\CasterInterface;

readonly class Date implements CasterInterface
{
    public function cast(mixed $value): CarbonInterface
    {
        return new Carbon($value);
    }
}
