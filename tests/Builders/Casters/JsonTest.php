<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Casters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use Terseq\Builders\Casters\Json;

#[CoversClass(Json::class)]
final class JsonTest extends TestCase
{
    public function testCastTransformsStringToJson(): void
    {
        $caster = new Json();
        $json = $caster->cast('{"key": "value"}');
        $this->assertInstanceOf(stdClass::class, $json);
        $this->assertEquals('value', $json->key);
    }

    public function testCastTransformsArrayToJson(): void
    {
        $caster = new Json(true);
        $json = $caster->cast('{"key": "value"}');
        $this->assertIsArray($json);
        $this->assertEquals('value', $json['key']);
    }

    public function testCastThrowsExceptionOnInvalidJson(): void
    {
        $caster = new Json();
        $this->expectException(\JsonException::class);
        $caster->cast('invalid json');
    }
}
