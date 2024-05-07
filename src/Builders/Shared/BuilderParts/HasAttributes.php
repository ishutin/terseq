<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

trait HasAttributes
{
    protected array $attributes = [];

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    protected function createAttribute(string $attribute): string
    {
        $generatedName = sprintf('#%s', $attribute);

        if (!isset($this->attributes[$generatedName])) {
            $this->attributes[$generatedName] = $attribute;
        }

        return $generatedName;
    }

}
