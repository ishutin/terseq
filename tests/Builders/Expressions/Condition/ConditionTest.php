<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Expressions\Condition;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Expressions\Condition\Condition;

#[CoversClass(Condition::class)]
final class ConditionTest extends TestCase
{
    public function testPrepareStartsWithTypeWhenNotFirst(): void
    {
        $condition = new class () extends Condition {
            public function prepare(bool $isFirst): string
            {
                return $this->getStartCondition($isFirst);
            }
        };

        $this->assertEquals(' AND ', $condition->prepare(false));
    }

    public function testPrepareStartsWithNotWhenIsNotCondition(): void
    {
        $condition = new class () extends Condition {
            public function prepare(bool $isFirst): string
            {
                $this->isNotCondition = true;
                return $this->getStartCondition($isFirst);
            }
        };

        $this->assertEquals('NOT ', $condition->prepare(true));
    }

    public function testPrepareStartsWithEmptyWhenIsFirst(): void
    {
        $condition = new class () extends Condition {
            public function prepare(bool $isFirst): string
            {
                return $this->getStartCondition($isFirst);
            }
        };

        $this->assertEquals('', $condition->prepare(true));
    }
}
