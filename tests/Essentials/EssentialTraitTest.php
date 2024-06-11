<?php

declare(strict_types=1);

namespace Terseq\Tests\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Builder;
use Terseq\Contracts\Dispatchers\DispatcherInterface;
use Terseq\Essentials\Essential;

#[CoversClass(Essential::class)]
#[UsesClass(Builder::class)]
final class EssentialTraitTest extends TestCase
{
    public function testDispatch(): void
    {
        $essential = new class () extends Builder {
            use Essential;

            public function getQuery(): array
            {
                return [];
            }
        };

        $dispatcher = $this->createStub(DispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturn('result');
        $essential->setDispatcher($dispatcher);

        $this->assertEquals('result', $essential->dispatch());
    }

    public function testDispatchAsync(): void
    {
        $essential = new class () extends Builder {
            use Essential;

            public function getQuery(): array
            {
                return [];
            }
        };

        $promise = $this->createStub(PromiseInterface::class);

        $dispatcher = $this->createStub(DispatcherInterface::class);
        $dispatcher->method('async')->willReturn($promise);
        $essential->setDispatcher($dispatcher);

        $this->assertSame($promise, $essential->dispatchAsync());
    }
}
