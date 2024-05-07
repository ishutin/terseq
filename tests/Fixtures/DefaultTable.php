<?php

declare(strict_types=1);

namespace Terseq\Tests\Fixtures;

use Terseq\Builders\Table;

class DefaultTable extends Table
{
    public function getTableName(): string
    {
        return 'default_table';
    }
}
