<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Expressions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Expressions\ConditionExpression;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Shared\ValuesStorage;

#[CoversClass(ConditionExpression::class)]
#[UsesClass(ConditionItem::class)]
final class ConditionExpressionTest extends TestCase
{
    public function testAttributeTypeAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new ConditionExpression($valuesStorage);

        $expression->attributeType('attribute', 'value');

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }

    public function testSizeAddsCondition(): void
    {
        $valuesStorage = $this->createMock(ValuesStorage::class);
        $expression = new ConditionExpression($valuesStorage);

        $expression->size('attribute', ComparisonOperator::EQ->value, 1);

        $reflection = new \ReflectionClass($expression);
        $property = $reflection->getProperty('conditions');
        $property->setAccessible(true);
        $conditions = $property->getValue($expression);

        $this->assertCount(1, $conditions);
        $this->assertInstanceOf(ConditionItem::class, $conditions[0]);
    }
}
