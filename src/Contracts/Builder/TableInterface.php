<?php

declare(strict_types=1);

namespace Terseq\Contracts\Builder;

interface TableInterface
{
    public function getTableName(): string;

    public function getPartitionKey(): string;

    public function getSortKey(): string;
}
