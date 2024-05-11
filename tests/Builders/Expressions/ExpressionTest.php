<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Expressions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Expressions\Condition\Condition;
use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Expressions\Expression;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Shared\ValuesStorage;

#[CoversClass(Expression::class)]
#[UsesClass(ConditionItem::class)]
final class ExpressionTest extends TestCase
{
    public function testPrepareConcatenatesConditions(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new class ($valuesStorage) extends Expression {};

        $condition1 = $this->createMock(Condition::class);
        $condition1->method('prepare')->willReturn('condition1');
        $condition2 = $this->createMock(Condition::class);
        $condition2->method('prepare')->willReturn('condition2');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $property->setValue($expression, [$condition1, $condition2]);

        $this->assertEquals('condition1condition2', $expression->prepare());
    }

    public function testIsEmptyReturnsTrueWhenNoConditions(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new class ($valuesStorage) extends Expression {};

        $this->assertTrue($expression->isEmpty());
    }

    public function testIsEmptyReturnsFalseWhenThereAreConditions(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new class ($valuesStorage) extends Expression {};

        $condition = $this->createMock(Condition::class);

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $property->setValue($expression, [$condition]);

        $this->assertFalse($expression->isEmpty());
    }

    public function testGetConditionsReturnsConditions(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new class ($valuesStorage) extends Expression {};

        $condition = $this->createMock(Condition::class);

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $property->setValue($expression, [$condition]);

        $this->assertEquals([$condition], $expression->getConditions());
    }

    public function testSimpleConditionAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new class ($valuesStorage) extends Expression {
            public function publicSimpleCondition(string $attribute, mixed $value, ComparisonOperator $operator, ?string $type = 'AND', ?bool $not = false): static
            {
                return $this->simpleCondition($attribute, $value, $operator, $type, $not);
            }
        };

        $expression->publicSimpleCondition('attribute', 'value', ComparisonOperator::EQ);

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(Condition::class, $conditions[0]);
    }
}
