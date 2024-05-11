<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Table;

#[CoversClass(Table::class)]
#[UsesClass(Keys::class)]
final class TableTest extends TestCase
{
    public function testConstruct(): void
    {
        $table = new class () extends Table {
            public function getTableName(): string
            {
                return 'test-table';
            }

            public function getKeys(): Keys
            {
                return new Keys(partitionKey: 'id');
            }
        };

        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals('test-table', $table->getTableName());
        $this->assertEquals('id', $table->getKeysFromMemory()->partitionKey);
        $this->assertEquals('id', $table->getKeys()->partitionKey);
        $this->assertNull($table->getSecondaryIndexMap());
        $this->assertNull($table->getSecondaryIndexMapFromMemory());
    }
}
