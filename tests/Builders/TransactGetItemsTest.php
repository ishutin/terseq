<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\TransactGetItems\Operations\Get;
use Terseq\Builders\Table;
use Terseq\Builders\TransactGetItems;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(TransactGetItems::class)]
#[CoversClass(Get::class)]
#[UsesClass(Keys::class)]
#[UsesClass(Table::class)]
final class TransactGetItemsTest extends TestCase
{
    public function testFullQuery(): void
    {
        $table = new BooksTable();

        $builder = (new TransactGetItems())
            ->get(
                fn (Get $get) => $get->table($table)
                ->composite('book-id-for-get', 'release-date')
                ->projectionExpression(['BookId', 'ReleaseDate', 'Price', 'Color', 'IsHidden', 'NotForSale']),
            )
            ->get(
                fn (Get $get) => $get->table($table)
                ->pk('book-id-for-get-2')
                ->projectionExpression(['NotForSale']),
            );

        $this->assertEquals([
            'TransactItems' => [
                [
                    'Get' => [
                        'TableName' => 'Books',
                        'Key' => [
                            'BookId' => [
                                'S' => 'book-id-for-get',
                            ],
                            'ReleaseDate' => [
                                'S' => 'release-date',
                            ],
                        ],
                        'ProjectionExpression' => '#BookId, #ReleaseDate, #Price, #Color, #IsHidden, #NotForSale',
                        'ExpressionAttributeNames' => [
                            '#BookId' => 'BookId',
                            '#ReleaseDate' => 'ReleaseDate',
                            '#Price' => 'Price',
                            '#Color' => 'Color',
                            '#IsHidden' => 'IsHidden',
                            '#NotForSale' => 'NotForSale',
                        ],
                    ],
                ],
                [
                    'Get' => [
                        'TableName' => 'Books',
                        'Key' => [
                            'BookId' => [
                                'S' => 'book-id-for-get-2',
                            ],
                        ],
                        'ProjectionExpression' => '#NotForSale',
                        'ExpressionAttributeNames' => [
                            '#NotForSale' => 'NotForSale',
                        ],
                    ],
                ],
            ],
        ], $builder->getQuery());
    }
}
