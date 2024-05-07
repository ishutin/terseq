<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\Enums;

enum ReturnConsumedCapacity: string
{
    case Indexes = 'INDEXES';
    case Total = 'TOTAL';
    case None = 'NONE';
}
