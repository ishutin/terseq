<?php

declare(strict_types=1);

namespace Terseq\Tests\Builder\GetItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Operations\GetItem\GetItem;
use Terseq\Builders\Table;

#[CoversClass(GetItem::class)]
class GetItemTest extends TestCase
{
    public function testCreate(): void
    {
        $builder = new GetItem();
        self::assertInstanceOf(GetItem::class, $builder);
    }

    public function testEmptyGetQuery(): void
    {
        $builder = new GetItem();

        $this->expectException(BuilderException::class);
        $builder->getQuery();
    }

    public function testGetQueryWithoutKey(): void
    {
        $builder = new GetItem(
            table: new class () extends Table {
                public function getTableName(): string
                {
                    return 'table';
                }
            },
        );

        $this->expectException(BuilderException::class);
        $builder->getQuery();
    }
}
