<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Shared\Extend;

use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Operations\Query\Enums\ComparisonOperator;
use Terseq\Builders\Shared\Extends\RenderCondition;

#[CoversClass(RenderCondition::class)]
final class RenderConditionTest extends TestCase
{
    public static function provideData(): Generator
    {
        yield [
            'expected' => 'attribute = value',
            'operator' => ComparisonOperator::EQ,
            'values' => ['value'],
            'attribute' => 'attribute',
            'additionalOperator' => null,
        ];

        yield [
            'expected' => 'size(attribute) >= value',
            'operator' => ComparisonOperator::GE,
            'values' => ['value'],
            'attribute' => 'attribute',
            'additionalOperator' => ComparisonOperator::SIZE,
        ];

        yield [
            'expected' => 'begins_with(attribute, value)',
            'operator' => ComparisonOperator::BEGINS_WITH,
            'values' => ['value'],
            'attribute' => 'attribute',
            'additionalOperator' => null,
        ];

        yield [
            'expected' => 'contains(attribute, value)',
            'operator' => ComparisonOperator::CONTAINS,
            'values' => ['value'],
            'attribute' => 'attribute',
            'additionalOperator' => null,
        ];

        yield [
            'expected' => 'attribute IN (value1, value2)',
            'operator' => ComparisonOperator::IN,
            'values' => ['value1', 'value2'],
            'attribute' => 'attribute',
            'additionalOperator' => null,
        ];

        yield [
            'expected' => 'attribute BETWEEN value1 AND value2',
            'operator' => ComparisonOperator::BETWEEN,
            'values' => ['value1', 'value2'],
            'attribute' => 'attribute',
            'additionalOperator' => null,
        ];
    }

    #[DataProvider('provideData')]
    public function testRenderCondition(
        string $expected,
        ComparisonOperator $operator,
        array $values,
        string $attribute,
        ?ComparisonOperator $additionalOperator,
    ): void {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->assertSame(
            $expected,
            $renderCondition->renderCondition($operator, $values, $attribute, $additionalOperator),
        );
    }

    public function testBetween1(): void
    {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->expectException(BuilderException::class);

        $renderCondition->renderCondition(
            ComparisonOperator::BETWEEN,
            [],
            'attribute',
        );
    }

    public function testBetween2(): void
    {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->expectException(BuilderException::class);

        $renderCondition->renderCondition(
            ComparisonOperator::BETWEEN,
            ['test'],
            'attribute',
        );
    }

    public function testBetween3(): void
    {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->expectException(BuilderException::class);

        $renderCondition->renderCondition(
            ComparisonOperator::BETWEEN,
            ['test', 'test', 'test'],
            'attribute',
        );
    }


    public function testEmptyValues1(): void
    {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->expectException(BuilderException::class);

        $renderCondition->renderCondition(
            ComparisonOperator::CONTAINS,
            [],
            'attribute',
        );
    }

    public function testEmptyValues2(): void
    {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->expectException(BuilderException::class);

        $renderCondition->renderCondition(
            ComparisonOperator::EQ,
            [],
            'attribute',
        );
    }

    public function testEmptyValues3(): void
    {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->expectException(BuilderException::class);

        $renderCondition->renderCondition(
            ComparisonOperator::IN,
            [],
            'attribute',
        );
    }

    public function testWringComparsionOperator(): void
    {
        $renderCondition = new class {
            use RenderCondition;
        };

        $this->expectException(BuilderException::class);

        $renderCondition->renderCondition(
            ComparisonOperator::SIZE,
            ['test'],
            'attribute',
        );
    }
}