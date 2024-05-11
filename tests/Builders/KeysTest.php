<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;

#[CoversClass(Keys::class)]
final class KeysTest extends TestCase
{
    public function testKeysConstructorCreatesKeysWithPartitionKeyOnly(): void
    {
        $keys = new Keys('pk');
        $this->assertEquals(['pk', null], $keys->toArray());
    }

    public function testKeysConstructorCreatesKeysWithPartitionAndSortKey(): void
    {
        $keys = new Keys('pk', 'sk');
        $this->assertEquals(['pk', 'sk'], $keys->toArray());
    }
}
