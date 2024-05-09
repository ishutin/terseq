<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\TransactWriteItems;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\TransactWriteItems\Operations\ConditionCheck;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Delete;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Put;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Update;
use Terseq\Builders\Operations\TransactWriteItems\TransactWriteItems;
use Terseq\Builders\Shared\ValuesStorage;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(TransactWriteItems::class)]
#[UsesClass(ValuesStorage::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
#[CoversClass(ConditionCheck::class)]
#[CoversClass(Delete::class)]
#[CoversClass(Update::class)]
#[CoversClass(Put::class)]
final class TransactWriteItemsTest extends TestCase
{
    public function testFullQuery(): void
    {
        $table = new BooksTable();
        $builder = (new TransactWriteItems())
            ->conditionCheck(
                fn (ConditionCheck $conditionCheck) => $conditionCheck->table($table)
                ->pk('book-id-for-delete'),
            )
            ->put(
                fn (Put $put) => $put->table($table)
                ->item([
                    'BookId' => 'book-id-for-delete',
                    'ReleaseDate' => 'release-date',
                    'Price' => 20,
                    'Color' => 'Red',
                ]),
            )
            ->delete(
                fn (Delete $delete) => $delete->table($table)
                ->composite('book-id-for-delete', 'release-date'),
            )
            ->update(
                fn (Update $update) => $update->table($table)
                ->pk('book-id-for-delete')
                ->set('Price', 20)
                ->set('Color', 'Red')
                ->delete('IsHidden')
                ->remove('NotForSale'),
            );

        $this->assertEquals([
            'TransactItems' => [
                [
                    'Put' => [
                        'TableName' => 'Books',
                        'Item' => [
                            'BookId' => [
                                'S' => 'book-id-for-delete',
                            ],
                            'ReleaseDate' => [
                                'S' => 'release-date',
                            ],
                            'Price' => [
                                'N' => '20',
                            ],
                            'Color' => [
                                'S' => 'Red',
                            ],
                        ],
                    ],
                ],
                [
                    'Delete' => [
                        'TableName' => 'Books',
                        'Key' => [
                            'BookId' => [
                                'S' => 'book-id-for-delete',
                            ],
                            'ReleaseDate' => [
                                'S' => 'release-date',
                            ],
                        ],
                    ],
                ],
                [
                    'Update' => [
                        'TableName' => 'Books',
                        'Key' => [
                            'BookId' => [
                                'S' => 'book-id-for-delete',
                            ],
                        ],
                        'UpdateExpression' => 'SET #Price = :price_0, #Color = :color_0 DELETE #IsHidden REMOVE #NotForSale',
                        'ExpressionAttributeNames' => [
                            '#Price' => 'Price',
                            '#Color' => 'Color',
                            '#IsHidden' => 'IsHidden',
                            '#NotForSale' => 'NotForSale',
                        ],
                        'ExpressionAttributeValues' => [
                            ':price_0' => [
                                'N' => '20',
                            ],
                            ':color_0' => [
                                'S' => 'Red',
                            ],
                        ],
                    ],
                ],
                [
                    'ConditionCheck' => [
                        'TableName' => 'Books',
                        'Key' => [
                            'BookId' => [
                                'S' => 'book-id-for-delete',
                            ],
                        ],
                    ],
                ],
            ],
        ], $builder->getQuery());
    }
}
