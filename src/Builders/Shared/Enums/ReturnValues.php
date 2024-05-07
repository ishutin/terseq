<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\Enums;

enum ReturnValues: string
{
    case None = 'NONE';
    case AllOld = 'ALL_OLD';
    case UpdatedOld = 'UPDATED_OLD';
    case AllNew = 'ALL_NEW';
    case UpdatedNew = 'UPDATED_NEW';
}
