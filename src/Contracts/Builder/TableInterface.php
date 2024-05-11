<?php

declare(strict_types=1);

namespace Terseq\Contracts\Builder;

use Terseq\Builders\Keys;

interface TableInterface
{
    public function getTableName(): string;

    public function getKeys(): Keys;

    /**
     * @return array<string, Keys>|null
     */
    public function getSecondaryIndexMap(): ?array;

    public function getKeysFromMemory(): Keys;

    public function getSecondaryIndexMapFromMemory(): ?array;
}
