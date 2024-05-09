<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\BatchGetItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\BatchGetItem\BatchGetItem;
use Terseq\Builders\Operations\BatchGetItem\Operations\BatchGet;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(BatchGetItem::class)]
#[CoversClass(BatchGet::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
final class BatchGetItemTest extends TestCase
{
    public function testFullQuery(): void
    {
        $builder = (new BatchGetItem())
            ->table(new BooksTable())
            ->returnConsumedCapacity(ReturnConsumedCapacity::Total)
            ->get(
                fn (BatchGet $get) => $get
                ->pk('first-book-id')
                ->pk('second-book-id')
                ->composite('book-id', 'release-date'),
            );

        $this->assertEquals([
            'ReturnConsumedCapacity' => 'TOTAL',
            'RequestItems' => [
                'Books' => [
                    'Keys' => [
                        [
                            'BookId' => ['S' => 'first-book-id'],
                        ],
                        [
                            'BookId' => ['S' => 'second-book-id'],
                        ],
                        [
                            'BookId' => ['S' => 'book-id'],
                            'ReleaseDate' => ['S' => 'release-date'],
                        ],
                    ],
                ],
            ],
        ], $builder->getQuery());
    }
}
