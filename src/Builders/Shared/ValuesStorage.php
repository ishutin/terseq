<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared;

class ValuesStorage
{
    protected array $values = [];

    public function getValues(): array
    {
        return array_merge(...array_values($this->values));
    }

    public function createValue(string $attribute, mixed $value): string
    {
        if (!isset($this->values[$attribute])) {
            $this->values[$attribute] = [];
        }

        $count = count($this->values[$attribute]);

        $generatedName = mb_strtolower(
            sprintf(
                ':%s_%s',
                $attribute,
                $count,
            ),
        );

        $this->values[$attribute][$generatedName] = $value;

        return $generatedName;
    }
}
