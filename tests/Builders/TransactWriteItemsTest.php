<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Expressions\Condition\Condition;
use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Expressions\ConditionExpression;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\TransactWriteItems\Operations\ConditionCheck;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Delete;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Put;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Update;
use Terseq\Builders\Shared\Extends\RenderCondition;
use Terseq\Builders\Shared\ValuesStorage;
use Terseq\Builders\Table;
use Terseq\Builders\TransactWriteItems;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(TransactWriteItems::class)]
#[UsesClass(ValuesStorage::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
#[CoversClass(ConditionCheck::class)]
#[CoversClass(Delete::class)]
#[CoversClass(Update::class)]
#[CoversClass(Put::class)]
#[CoversClass(ConditionExpression::class)]
#[UsesClass(Condition::class)]
#[UsesClass(ConditionItem::class)]
#[UsesClass(RenderCondition::class)]
final class TransactWriteItemsTest extends TestCase
{
    public function testFullQuery(): void
    {
        $table = new BooksTable();
        $builder = (new TransactWriteItems())
            ->conditionCheck(
                fn (ConditionCheck $conditionCheck) => $conditionCheck->table($table)
                    ->conditionExpression(
                        static fn (ConditionExpression $condition) => $condition
                        ->between('Price', 10, 20)
                        ->attributeExists('Color')
                        ->size('Author', '>=', 5),
                    )
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
                        'ConditionExpression' => '#Price BETWEEN :price_0 AND :price_1 AND attribute_exists(#Color) AND size(#Author) >= :author_0',
                        "ExpressionAttributeNames" => [
                            "#Price" => "Price",
                            "#Color" => "Color",
                            "#Author" => "Author",
                        ],
                        "ExpressionAttributeValues" => [
                            ":price_0" => [
                                "N" => "10",
                            ],
                            ":price_1" => [
                                "N" => "20",
                            ],
                            ":author_0" => [
                                "N" => "5",
                            ],
                        ],
                    ],
                ],
            ],
        ], $builder->getQuery());
    }

    public function testMultiTables(): void
    {
        $table1 = ['Table1', 'BookId', 'ReleaseDate'];
        $table2 = ['Table2', 'BookId', 'ReleaseDate'];
        $table3 = ['Table3', 'BookId', 'ReleaseDate'];
        $table4 = ['Table4', 'BookId', 'ReleaseDate'];

        $builder = (new TransactWriteItems())
            ->conditionCheck(
                fn (ConditionCheck $conditionCheck) => $conditionCheck->table($table1)
                    ->pk('book-id-for-delete'),
            )
            ->put(
                fn (Put $put) => $put->table($table2)
                    ->item([
                        'BookId' => 'book-id-for-delete',
                        'ReleaseDate' => 'release-date',
                        'Price' => 20,
                        'Color' => 'Red',
                    ]),
            )
            ->delete(
                fn (Delete $delete) => $delete->table($table3)
                    ->composite('book-id-for-delete', 'release-date'),
            )
            ->update(
                fn (Update $update) => $update->table($table4)
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
                        'TableName' => 'Table2',
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
                        'TableName' => 'Table3',
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
                        'TableName' => 'Table4',
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
                        'TableName' => 'Table1',
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

    public function testSingleTable(): void
    {
        $table = new BooksTable();
        $builder = (new TransactWriteItems(table: $table))
            ->conditionCheck(
                fn (ConditionCheck $conditionCheck) => $conditionCheck
                    ->pk('book-id-for-delete'),
            )
            ->put(
                fn (Put $put) => $put
                    ->item([
                        'BookId' => 'book-id-for-delete',
                        'ReleaseDate' => 'release-date',
                        'Price' => 20,
                        'Color' => 'Red',
                    ]),
            )
            ->delete(
                fn (Delete $delete) => $delete
                    ->composite('book-id-for-delete', 'release-date'),
            )
            ->update(
                fn (Update $update) => $update
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
