<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Shared\Extend;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Shared\Extends\When;

#[CoversClass(When::class)]
final class WhenTest extends TestCase
{
    public function testWhenConditionIsTrueCallbackIsCalled(): void
    {
        $when = new class () {
            use When;
        };

        $callbackCalled = false;
        $callback = function ($clone) use (&$callbackCalled) {
            $callbackCalled = true;
            return $clone;
        };

        $when->when(true, $callback);

        $this->assertTrue($callbackCalled);
    }

    public function testWhenConditionIsFalseCallbackIsNotCalled(): void
    {
        $when = new class () {
            use When;
        };

        $callbackCalled = false;
        $callback = function ($clone) use (&$callbackCalled) {
            $callbackCalled = true;
            return $clone;
        };

        $when->when(false, $callback);

        $this->assertFalse($callbackCalled);
    }
}
