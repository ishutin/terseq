<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use Terseq\Builders\Shared\Enums;

trait ReturnItemCollectionMetrics
{
    protected ?Enums\ReturnItemCollectionMetrics $returnItemCollectionMetrics = null;

    public function returnItemCollectionMetrics(Enums\ReturnItemCollectionMetrics $metrics): static
    {
        $clone = clone $this;
        $clone->returnItemCollectionMetrics = $metrics;

        return $clone;
    }

    protected function appendReturnItemCollectionMetrics(array $config): array
    {
        if ($this->returnItemCollectionMetrics) {
            $config['ReturnItemCollectionMetrics'] = $this->returnItemCollectionMetrics->value;
        }

        return $config;
    }
}
