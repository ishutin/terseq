<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Terseq\Contracts\Builder\TableInterface;

abstract class Table implements TableInterface
{
    protected ?Keys $keys = null;
    protected ?array $secondaryIndexMap = null;

    public function getSecondaryIndexMap(): ?array
    {
        return null;
    }

    public function getSecondaryIndexMapFromMemory(): ?array
    {
        if (!$this->secondaryIndexMap) {
            $this->secondaryIndexMap = $this->getSecondaryIndexMap();
        }

        return $this->secondaryIndexMap;
    }

    public function getKeysFromMemory(): Keys
    {
        if (!$this->keys) {
            $this->keys = $this->getKeys();
        }

        return $this->keys;
    }
}
