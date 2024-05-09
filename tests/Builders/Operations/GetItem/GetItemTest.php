<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\GetItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\GetItem\GetItem;
use Terseq\Builders\Shared\ValuesStorage;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(GetItem::class)]
#[UsesClass(Keys::class)]
#[UsesClass(Table::class)]
#[UsesClass(ValuesStorage::class)]
final class GetItemTest extends TestCase
{
    public function testFullQuery(): void
    {
        $table = new BooksTable();
        $builder = new GetItem(table: $table);
        $builder = $builder
            ->composite('book-id-for-get', 'release-date')
            ->setConsistentRead(true)
            ->projectionExpression(['Author', 'Title']);

        $this->assertEquals([
            'TableName' => 'Books',
            'Key' => [
                'BookId' => [
                    'S' => 'book-id-for-get',
                ],
                'ReleaseDate' => [
                    'S' => 'release-date',
                ],
            ],
            'ConsistentRead' => true,
            'ExpressionAttributeNames' => [
                '#Author' => 'Author',
                '#Title' => 'Title',
            ],
            'ProjectionExpression' => '#Author, #Title',
            'ExpressionAttributeValues' => [],
        ], $builder->getQuery());
    }
}
