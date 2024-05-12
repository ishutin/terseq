<?php

declare(strict_types=1);

namespace Terseq\Builders\Casters;

use Terseq\Contracts\Dispatchers\Casters\CasterInterface;

class Caster
{
    /**
     * @param array<string, CasterInterface> $casters
     */
    public function __construct(
        protected array $casters = [],
    ) {
    }

    public function getCasters(): array
    {
        return $this->casters;
    }

    public function add(string $attribute, CasterInterface $caster): static
    {
        $clone = clone $this;
        $clone->casters[$attribute] = $caster;

        return $clone;
    }

    public function castItem(array $item): array
    {
        foreach ($item as $key => $value) {
            if (isset($this->casters[$key])) {
                $item[$key] = $this->casters[$key]->cast($value);
            }
        }

        return $item;
    }
}
