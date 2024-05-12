<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\Query;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Operations\Query\SortKeyCondition;

#[CoversClass(SortKeyCondition::class)]
final class SortKeyConditionTest extends TestCase
{
    private SortKeyCondition $sortKeyCondition;

    protected function setUp(): void
    {
        $this->sortKeyCondition = new SortKeyCondition();
    }

    public function testEqual(): void
    {
        $result = $this->sortKeyCondition->equal('value', 'attribute');
        $this->assertEquals(['attribute', ['value'], ComparisonOperator::EQ], $result->getQueryData());
    }

    public function testLessThan(): void
    {
        $result = $this->sortKeyCondition->lessThan('value', 'attribute');
        $this->assertEquals(['attribute', ['value'], ComparisonOperator::LT], $result->getQueryData());
    }

    public function testLessOrEqual(): void
    {
        $result = $this->sortKeyCondition->lessOrEqual('value', 'attribute');
        $this->assertEquals(['attribute', ['value'], ComparisonOperator::LE], $result->getQueryData());
    }

    public function testGreaterThan(): void
    {
        $result = $this->sortKeyCondition->greaterThan('value', 'attribute');
        $this->assertEquals(['attribute', ['value'], ComparisonOperator::GT], $result->getQueryData());
    }

    public function testGreaterOrEqual(): void
    {
        $result = $this->sortKeyCondition->greaterOrEqual('value', 'attribute');
        $this->assertEquals(['attribute', ['value'], ComparisonOperator::GE], $result->getQueryData());
    }

    public function testBetween(): void
    {
        $result = $this->sortKeyCondition->between('from', 'to', 'attribute');
        $this->assertEquals(['attribute', ['from', 'to'], ComparisonOperator::BETWEEN], $result->getQueryData());
    }

    public function testBeginsWith(): void
    {
        $result = $this->sortKeyCondition->beginsWith('value', 'attribute');
        $this->assertEquals(['attribute', ['value'], ComparisonOperator::BEGINS_WITH], $result->getQueryData());
    }
}
