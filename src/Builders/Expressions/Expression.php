<?php

declare(strict_types=1);

namespace Terseq\Builders\Expressions;

use Terseq\Builders\Expressions\Condition\Condition;
use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\Extends\When;
use Terseq\Builders\Shared\ValuesStorage;

abstract class Expression
{
    use HasAttributes;
    use When;

    /**
     * @var Condition[]
     */
    protected array $conditions = [];

    public function __construct(
        protected readonly ValuesStorage $valuesStorage,
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
        $condition = new ConditionItem(
            attribute: $this->createAttribute($attribute),
            values: [
                $this->valuesStorage->createValue($attribute, $value),
            ],
            operator: $operator,
            type: $type,
            isNotCondition: $not,
        );

        $this->conditions[] = $condition;

        return $this;
    }
}
