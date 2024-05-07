<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

trait PutItem
{
    protected array $item = [];

    public function item(array $item): static
    {
        $clone = clone $this;
        $clone->item = $item;

        return $clone;
    }

    protected function appendItem(array $config): array
    {
        if ($this->item) {
            $config['Item'] = $this->marshaler->marshalItem($this->item);
        }

        return $config;
    }
}
