<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

trait AppendAttributes
{
    protected function appendAttributes(array $config, bool $withValues = true): array
    {
        if ($this->attributes) {
            $config['ExpressionAttributeNames'] = $this->attributes;

            if ($withValues) {
                $config['ExpressionAttributeValues'] = $this->marshaler->marshalItem($this->valuesStorage->getValues());
            }
        }

        return $config;
    }
}
