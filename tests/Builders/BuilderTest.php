<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use Aws\DynamoDb\Marshaler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Builder;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Keys;
use Terseq\Builders\Table;
use Terseq\Contracts\Builder\TableInterface;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(Builder::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
final class BuilderTest extends TestCase
{
    public function testConstructorWithNullTable(): void
    {
        $builder = $this->getBuilder();
        $this->assertNull($builder->table);
    }

    public function testConstructorWithArrayTable1(): void
    {
        $this->expectException(BuilderException::class);
        $this->getBuilder(['testTable']);
    }

    public function testConstructorWithArrayTable2(): void
    {
        $builder = $this->getBuilder(['testTable', 'Pk']);
        $this->assertEquals('testTable', $builder->table->getTableName());
        $this->assertEquals('Pk', $builder->table->getKeys()->partitionKey);
        $this->assertNull($builder->table->getKeys()->sortKey);
    }

    public function testConstructorWithArrayTable3(): void
    {
        $builder = $this->getBuilder(['testTable', 'pk', 'sk']);
        $this->assertEquals('testTable', $builder->table->getTableName());
        $this->assertEquals('pk', $builder->table->getKeys()->partitionKey);
        $this->assertEquals('sk', $builder->table->getKeys()->sortKey);
    }

    public function testConstructorWithArrayTable4(): void
    {
        $builder = $this->getBuilder(
            [
                'table' => 'testTable',
                'pk' => 'Pk',
                'sk' => 'Sk',
            ],
        );
        $this->assertEquals('Pk', $builder->table->getKeys()->partitionKey);
        $this->assertEquals('Sk', $builder->table->getKeys()->sortKey);
    }

    public function testWithTableObject(): void
    {
        $table = new BooksTable();

        $builder = $this->getBuilder($table);
        $this->assertEquals($table, $builder->table);
        $this->assertEquals('Books', $builder->table->getTableName());
        $this->assertEquals('BookId', $builder->table->getKeys()->partitionKey);
        $this->assertEquals('ReleaseDate', $builder->table->getKeys()->sortKey);
    }

    public function testSetTableWhenTableIsDefined(): void
    {
        $table = new BooksTable();
        $builder = $this->getBuilder($table);
        $builder->table($table);
        $builder->table(['testTable', 'Pk']);

        $this->assertSame($table, $builder->table);
    }

    public function testNotCreateCloneWhenTableDefinedNull(): void
    {
        $builder = $this->getBuilder();
        $this->assertSame($builder, $builder->table(null));
    }

    protected function getBuilder(
        TableInterface|array|null $table = null,
        Marshaler $marshaler = new Marshaler(),
    ): Builder {
        return new class ($table, $marshaler) extends Builder {
            public function getQuery(): array
            {
                return [];
            }
        };
    }
}
