<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use function implode;
use function is_string;

trait ProjectionExpression
{
    protected array $projectionExpression = [];

    public function projectionExpression(array|string $attributes): static
    {
        $clone = clone $this;

        $attributes = is_string($attributes) ? [$attributes] : $attributes;

        foreach ($attributes as $attribute) {
            $clone->projectionExpression[] = $attribute;
        }

        return $clone;
    }

    protected function appendProjectionExpression(array $config): array
    {
        if ($this->projectionExpression) {
            $attributes = [];

            foreach ($this->projectionExpression as $attribute) {
                $attributeName = $this->createAttribute($attribute);
                $attributes[] = $attributeName;
                $config['ExpressionAttributeNames'][$attributeName] = $attribute;
            }

            $config['ProjectionExpression'] = implode(', ', $attributes);
        }

        return $config;
    }
}
