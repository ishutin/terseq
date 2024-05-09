<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\BatchWriteItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Enums\ReturnItemCollectionMetrics;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(BatchWriteItem::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
final class BatchWriteItemTest extends TestCase
{
    public function testFullQuery(): void
    {
        $table = new BooksTable();
        $builder = (new BatchWriteItem())
            ->delete('book-id-for-delete', table: $table)
            ->deleteComposite('book-id', 'release-date', table: $table)
            ->returnItemCollectionMetrics(ReturnItemCollectionMetrics::Size)
            ->returnConsumedCapacity(ReturnConsumedCapacity::Indexes)
            ->put([
                'BookId' => 'first-book-id',
                'Title' => 'First Book',
                'AuthorId' => 'author-id',
                'ReleaseDate' => 'release-date',
            ], table: $table);

        $this->assertEquals(
            [
                'ReturnConsumedCapacity' => 'INDEXES',
                'ReturnItemCollectionMetrics' => 'SIZE',
                'RequestItems' =>
                    [
                        'Books' =>
                            [
                                [
                                    'PutRequest' =>
                                        [
                                            'Item' =>
                                                [
                                                    'BookId' =>
                                                        [
                                                            'S' => 'first-book-id',
                                                        ],
                                                    'Title' =>
                                                        [
                                                            'S' => 'First Book',
                                                        ],
                                                    'AuthorId' =>
                                                        [
                                                            'S' => 'author-id',
                                                        ],
                                                    'ReleaseDate' =>
                                                        [
                                                            'S' => 'release-date',
                                                        ],
                                                ],
                                        ],
                                ],
                                [
                                    'DeleteRequest' =>
                                        [
                                            'Key' =>
                                                [
                                                    'BookId' =>
                                                        [
                                                            'S' => 'book-id-for-delete',
                                                        ],
                                                ],
                                        ],
                                ],
                                [
                                    'DeleteRequest' =>
                                        [
                                            'Key' =>
                                                [
                                                    'BookId' =>
                                                        [
                                                            'S' => 'book-id',
                                                        ],
                                                ],
                                        ],
                                ],
                                [
                                    'DeleteRequest' =>
                                        [
                                            'Key' =>
                                                [
                                                    'ReleaseDate' =>
                                                        [
                                                            'S' => 'release-date',
                                                        ],
                                                ],
                                        ],
                                ],
                            ],
                    ],
            ],
            $builder->getQuery(),
        );
    }

    public function testMore25Items(): void
    {
        $table = new BooksTable();
        $builder = new BatchWriteItem();

        for ($i = 0; $i < 30; $i++) {
            $builder = $builder->delete('book-id-for-delete', table: $table);
        }

        $this->expectException(BuilderException::class);
        $builder->getQuery();
    }
}
