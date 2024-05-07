<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Expressions;

use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Operations\Query\Expressions\Condition\Condition;
use Terseq\Builders\Operations\Query\Expressions\Condition\ConditionItem;
use Terseq\Builders\Operations\Query\Query;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;

abstract class Expression
{
    use HasAttributes;

    /**
     * @var Condition[]
     */
    protected array $conditions = [];

    public function __construct(
        protected readonly Query $query,
    ) {
    }

    public function prepare(): string
    {
        $result = '';

        foreach ($this->conditions as $index => $condition) {
            $result .= $condition->prepare(isFirst: $index === 0);
        }

        return $result;
    }

    public function isEmpty(): bool
    {
        return empty($this->conditions);
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    protected function simpleCondition(
        string $attribute,
        mixed $value,
        ComparisonOperator $operator,
        ?string $type = 'AND',
        ?bool $not = false,
    ): static {
        $this->modifyAttributeAndValue($attribute, $value);

        $condition = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [
                $this->query->valuesStorage->createValue($attribute, $value),
            ],
            operator: $operator,
            type: $type,
            isNotCondition: $not,
        );

        $this->conditions[] = $condition;

        return $this;
    }

    protected function modifyAttributeAndValue(string &$attribute, mixed &$value = null): void
    {
        $defaultAttributeName = $this->getDefaultAttributeName();

        if ($value === null && $defaultAttributeName !== null) {
            $value = $attribute;
            $attribute = $defaultAttributeName;
        }
    }

    protected function getDefaultAttributeName(): ?string
    {
        return null;
    }
}
