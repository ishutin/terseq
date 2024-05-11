<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Expressions\Condition;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;

#[CoversClass(ConditionItem::class)]
final class ConditionItemTest extends TestCase
{
    public function testPrepareReturnsCorrectStringForSingleValue(): void
    {
        $condition = new ConditionItem('attribute', ['value'], ComparisonOperator::EQ);

        $this->assertEquals('attribute = value', $condition->prepare(true));
    }

    public function testPrepareReturnsCorrectStringForMultipleValues(): void
    {
        $condition = new ConditionItem('attribute', ['value1', 'value2'], ComparisonOperator::IN);

        $this->assertEquals('attribute IN (value1, value2)', $condition->prepare(true));
    }

    public function testPrepareReturnsCorrectStringForNotCondition(): void
    {
        $condition = new ConditionItem('attribute', ['value'], ComparisonOperator::EQ, 'AND', true);

        $this->assertEquals('NOT attribute = value', $condition->prepare(true));
    }

    public function testPrepareReturnsCorrectStringForAdditionalOperator(): void
    {
        $condition = new ConditionItem(
            'attribute',
            ['value1', 'value2'],
            ComparisonOperator::BETWEEN,
            'AND',
            false,
        );

        $this->assertEquals('attribute BETWEEN value1 AND value2', $condition->prepare(true));
    }
}
