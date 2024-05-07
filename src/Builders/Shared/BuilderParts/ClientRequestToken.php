<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

trait ClientRequestToken
{
    protected ?string $clientRequestToken = null;

    public function clientRequestToken(string $clientRequestToken): static
    {
        $clone = clone $this;
        $clone->clientRequestToken = $clientRequestToken;

        return $clone;
    }

    protected function appendClientRequestToken(array $config): array
    {
        if ($this->clientRequestToken !== null) {
            $config['ClientRequestToken'] = $this->clientRequestToken;
        }

        return $config;
    }
}
