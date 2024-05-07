<?php

declare(strict_types=1);

namespace Terseq\Contracts\Builder;

interface BuilderInterface
{
    public function getQuery(): array;
}
