<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Casters;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Casters\Date;

#[CoversClass(Date::class)]
final class DateTest extends TestCase
{
    public function testCastTransformsStringToDate(): void
    {
        $caster = new Date();
        $date = $caster->cast('2022-01-01');
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals('2022-01-01', $date->toDateString());
    }

    public function testCastTransformsDateTimeStringToDate(): void
    {
        $caster = new Date();
        $date = $caster->cast('2022-01-01 12:00:00');
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals('2022-01-01 12:00:00', $date->toDateTimeString());
    }
}
