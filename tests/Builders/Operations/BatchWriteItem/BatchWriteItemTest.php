<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\BatchWriteItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Enums\ReturnItemCollectionMetrics;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BookTable;


#[CoversClass(BatchWriteItem::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
class BatchWriteItemTest extends TestCase
{
    public function testFullQuery(): void
    {
        $builder = (new BatchWriteItem(
            table: new BookTable(),
        ))
            ->delete('book-id-for-delete')
            ->deleteComposite('book-id', 'release-date')
            ->returnItemCollectionMetrics(ReturnItemCollectionMetrics::Size)
            ->returnConsumedCapacity(ReturnConsumedCapacity::Indexes)
            ->put([
                'BookId' => 'first-book-id',
                'Title' => 'First Book',
                'AuthorId' => 'author-id',
                'ReleaseDate' => 'release-date',
            ]);

        $this->assertEquals([
            'TableName' => 'Books',
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
                                                'ReleaseDate' =>
                                                    [
                                                        'S' => 'release-date',
                                                    ],
                                            ],
                                    ],
                            ],
                        ],
                ],
        ], $builder->getQuery());
    }
}