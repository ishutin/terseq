<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

use function array_keys;
use function implode;
use function sprintf;

trait UpdateExpression
{
    protected array $set = [];
    protected array $add = [];
    protected array $delete = [];
    protected array $remove = [];

    public function set(string $attribute, mixed $value): static
    {
        $clone = clone $this;

        $clone->set[$clone->createAttribute($attribute)] = $clone->getValuesStorage()->createValue($attribute, $value);

        return $clone;
    }

    public function increment(string $attribute, int $value, ?string $counterAttribute = null): static
    {
        return $this->counterSet($attribute, $counterAttribute ?? $attribute, $value, '+');
    }

    public function decrement(string $attribute, int $value, ?string $counterAttribute = null): static
    {
        return $this->counterSet($attribute, $counterAttribute ?? $attribute, $value, '-');
    }

    public function setIfNotExists(string $attribute, mixed $value, ?string $expressionAttribute = null): static
    {
        $clone = clone $this;

        $attributeName = $clone->createAttribute($attribute);
        $expressionAttributeName = $attributeName;

        if ($expressionAttribute) {
            $expressionAttributeName = $clone->createAttribute($expressionAttribute);
        }

        $clone->set[$attributeName] = sprintf(
            'if_not_exists(%s, %s)',
            $expressionAttributeName,
            $clone->getValuesStorage()->createValue($attribute, $value),
        );

        return $clone;
    }

    public function setListAppend(string $attribute, mixed $value, ?string $expressionAttribute = null): static
    {
        $clone = clone $this;

        $attributeName = $clone->createAttribute($attribute);
        $expressionAttributeName = $attributeName;

        if ($expressionAttribute) {
            $expressionAttributeName = $clone->createAttribute($expressionAttribute);
        }

        $clone->set[$attributeName] = sprintf(
            'list_append(%s, %s)',
            $expressionAttributeName,
            $clone->getValuesStorage()->createValue($attribute, $value),
        );

        return $clone;
    }

    public function add(string $attribute, mixed $value): static
    {
        $clone = clone $this;

        $clone->add[$clone->createAttribute($attribute)] = $clone->getValuesStorage()->createValue($attribute, $value);

        return $clone;
    }

    public function delete(string $attribute): static
    {
        $clone = clone $this;

        $clone->delete[$clone->createAttribute($attribute)] = true;

        return $clone;
    }

    public function remove(string $attribute): static
    {
        $clone = clone $this;

        $clone->remove[$clone->createAttribute($attribute)] = true;

        return $clone;
    }

    protected function appendUpdateExpression(array $config): array
    {
        $updateExpressions = [];

        if (!empty($this->set)) {
            $updateExpressions[] = sprintf(
                'SET %s',
                $this->createUpdateExpression($this->set, ' = '),
            );
        }

        if (!empty($this->add)) {
            $updateExpressions[] = sprintf(
                'ADD %s',
                $this->createUpdateExpression($this->add),
            );
        }

        if (!empty($this->delete)) {
            $updateExpressions[] = sprintf(
                'DELETE %s',
                implode(
                    ', ',
                    array_keys($this->delete),
                ),
            );
        }

        if (!empty($this->remove)) {
            $updateExpressions[] = sprintf(
                'REMOVE %s',
                implode(
                    ', ',
                    array_keys($this->remove),
                ),
            );
        }

        if (!empty($updateExpressions)) {
            $config['UpdateExpression'] = implode(' ', $updateExpressions);
        }

        return $config;
    }

    protected function createUpdateExpression(array $add, string $string = ' '): string
    {
        $expressions = [];

        foreach ($add as $attribute => $value) {
            $expressions[] = sprintf(
                '%s%s%s',
                $attribute,
                $string,
                $value,
            );
        }

        return implode(', ', $expressions);
    }

    protected function counterSet(string $attribute, string $counterAttribute, int $value, string $type): static
    {
        $clone = clone $this;

        $clone->set[$clone->createAttribute($attribute)] = sprintf(
            '%s %s %s',
            $clone->createAttribute($counterAttribute),
            $type,
            $clone->getValuesStorage()->createValue(sprintf('%s_counter', $counterAttribute), $value),
        );

        return $clone;
    }
}
