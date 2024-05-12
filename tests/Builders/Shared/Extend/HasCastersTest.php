<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Shared\Extend;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Casters\Caster;
use Terseq\Builders\Shared\Extends\HasCasters;
use Terseq\Contracts\Dispatchers\Casters\CasterInterface;

#[CoversClass(HasCasters::class)]
#[UsesClass(Caster::class)]
final class HasCastersTest extends TestCase
{
    public function testAddCaster(): void
    {
        $builder = new class () {
            use HasCasters;
        };

        $builder = $builder
            ->addCaster('attribute', $this->createMock(CasterInterface::class));

        $this->assertInstanceOf(Caster::class, $builder->getCaster());
    }

    public function testAddCasterAddsCasterToExistingCasters(): void
    {
        $builder = new class () {
            use HasCasters;
        };

        $this->assertNull($builder->getCaster());
    }

    public function testAddMoreThanOne(): void
    {
        $builder = new class () {
            use HasCasters;
        };

        $builder = $builder
            ->addCaster('attribute1', $this->createMock(CasterInterface::class))
            ->addCaster('attribute2', $this->createMock(CasterInterface::class));

        $this->assertInstanceOf(Caster::class, $builder->getCaster());
        $this->assertCount(2, $builder->getCaster()->getCasters());
    }
}
