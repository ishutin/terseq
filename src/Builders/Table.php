<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Terseq\Contracts\Builder\TableInterface;

abstract class Table implements TableInterface
{
    public function getPartitionKey(): string
    {
        return 'PK';
    }

    public function getSortKey(): string
    {
        return 'SK';
    }
}
