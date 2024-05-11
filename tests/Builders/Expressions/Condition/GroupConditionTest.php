<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Expressions\Condition;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Expressions\Condition\Condition;
use Terseq\Builders\Expressions\Condition\GroupCondition;

#[CoversClass(GroupCondition::class)]
final class GroupConditionTest extends TestCase
{
    public function testPrepareReturnsCorrectStringForSingleCondition(): void
    {
        $condition = $this->createMock(Condition::class);
        $condition->method('prepare')->willReturn('condition');

        $groupCondition = new GroupCondition([$condition]);

        $this->assertEquals('(condition)', $groupCondition->prepare(true));
    }

    public function testPrepareReturnsCorrectStringForMultipleConditions(): void
    {
        $condition1 = $this->createMock(Condition::class);
        $condition1->method('prepare')->willReturn('condition1');
        $condition2 = $this->createMock(Condition::class);
        $condition2->method('prepare')->willReturn(' AND condition2');

        $groupCondition = new GroupCondition([$condition1, $condition2]);

        $this->assertEquals('(condition1 AND condition2)', $groupCondition->prepare(true));
    }

    public function testPrepareReturnsCorrectStringForNotFirstCondition(): void
    {
        $condition = $this->createMock(Condition::class);
        $condition->method('prepare')->willReturn('condition');

        $groupCondition = new GroupCondition([$condition], 'OR');

        $this->assertEquals(' OR (condition)', $groupCondition->prepare(false));
    }
}
