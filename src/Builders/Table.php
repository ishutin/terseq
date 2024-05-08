<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Terseq\Contracts\Builder\TableInterface;

abstract class Table implements TableInterface
{
    protected ?Keys $keys = null;
    protected ?array $globalSecondaryIndexMap = null;

    public function getGlobalSecondaryIndexMap(): ?array
    {
        return null;
    }

    public function getGlobalSecondaryIndexMapFromMemory(): ?array
    {
        if (!$this->globalSecondaryIndexMap) {
            $this->globalSecondaryIndexMap = $this->getGlobalSecondaryIndexMap();
        }

        return $this->globalSecondaryIndexMap;
    }

    public function getKeysFromMemory(): Keys
    {
        if (!$this->keys) {
            $this->keys = $this->getKeys();
        }

        return $this->keys;
    }
}
