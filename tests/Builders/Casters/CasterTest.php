<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Casters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Casters\Caster;
use Terseq\Contracts\Facades\Casters\CasterInterface;

#[CoversClass(Caster::class)]
final class CasterTest extends TestCase
{
    public function testCastersAreEmptyByDefault(): void
    {
        $caster = new Caster();
        $this->assertEquals([], $caster->getCasters());
    }

    public function testAddReturnsCloneWithNewCaster(): void
    {
        $caster = new Caster();
        $newCaster = $this->createMock(CasterInterface::class);
        $clone = $caster->add('attribute', $newCaster);
        $this->assertNotSame($caster, $clone);
        $this->assertEquals(['attribute' => $newCaster], $clone->getCasters());
    }

    public function testCastItemTransformsValuesWithCasters(): void
    {
        $item = ['attribute' => 'value'];
        $newCaster = $this->createMock(CasterInterface::class);
        $newCaster->method('cast')->willReturn('casted value');
        $caster = (new Caster())->add('attribute', $newCaster);
        $this->assertEquals(['attribute' => 'casted value'], $caster->castItem($item));
    }

    public function testCastItemDoesNotTransformValuesWithoutCasters(): void
    {
        $item = ['attribute' => 'value'];
        $caster = new Caster();
        $this->assertEquals($item, $caster->castItem($item));
    }
}
