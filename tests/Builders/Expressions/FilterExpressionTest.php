<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Expressions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Expressions\Condition\GroupCondition;
use Terseq\Builders\Expressions\FilterExpression;
use Terseq\Builders\Shared\ValuesStorage;

#[CoversClass(FilterExpression::class)]
#[UsesClass(ConditionItem::class)]
#[UsesClass(GroupCondition::class)]
final class FilterExpressionTest extends TestCase
{
    public function testGroupAddsGroupCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->group(function ($where) {
            $where->equal('attribute', 'value');
        });

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(GroupCondition::class, $conditions[0]);
    }

    public function testEqualAddsSimpleCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->equal('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testBetweenAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->between('attribute', 1, 10);

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testInAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->in('attribute', [1, 2, 3]);

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testLessThanAddsSimpleCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->lessThan('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testLessOrEqualAddsSimpleCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->lessOrEqual('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testGreaterThanAddsSimpleCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->greaterThan('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testGreaterOrEqualAddsSimpleCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->greaterOrEqual('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testNotEqualAddsSimpleCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->notEqual('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testBeginWithAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->beginsWith('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testAttributeNotExistsAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->attributeNotExists('attribute');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testContainsAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new FilterExpression($valuesStorage);

        $expression->contains('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }
}
