<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\UpdateItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\UpdateItem\UpdateItem;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Enums\ReturnItemCollectionMetrics;
use Terseq\Builders\Shared\Enums\ReturnValues;
use Terseq\Builders\Shared\ValuesStorage;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(UpdateItem::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
#[UsesClass(ValuesStorage::class)]
#[CoversClass(SingleWriteOperations::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnValues::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity::class)]
#[CoversClass(ReturnValuesOnConditionCheckFailure::class)]
final class UpdateItemTest extends TestCase
{
    public function testFullQuery(): void
    {
        $builder = (new UpdateItem())
            ->table(new BooksTable())
            ->returnConsumedCapacity(ReturnConsumedCapacity::Indexes)
            ->returnItemCollectionMetrics(ReturnItemCollectionMetrics::Size)
            ->returnValues(ReturnValues::AllOld)
            ->add('Countries', [
                'USA',
                'UK',
            ])
            ->add('Categories', [
                'IT',
                'AI',
                'Space',
            ])
            ->set('Price', 20)
            ->set('Color', 'Red')
            ->delete('IsHidden')
            ->remove('NotForSale')
            ->returnValuesOnConditionCheckFailure(ReturnValues::AllNew)
            ->composite('book-id-for-delete', 'release-date');

        $this->assertEquals([
            'TableName' => 'Books',
            'ReturnConsumedCapacity' => 'INDEXES',
            'ReturnItemCollectionMetrics' => 'SIZE',
            'ReturnValues' => 'ALL_OLD',
            'ReturnValuesOnConditionCheckFailure' => 'ALL_NEW',
            'UpdateExpression' => 'SET #Price = :price_0, #Color = :color_0 ADD #Countries :countries_0, #Categories :categories_0 DELETE #IsHidden REMOVE #NotForSale',
            'ExpressionAttributeNames' =>
                [
                    '#Countries' => 'Countries',
                    '#Categories' => 'Categories',
                    '#Price' => 'Price',
                    '#Color' => 'Color',
                    '#IsHidden' => 'IsHidden',
                    '#NotForSale' => 'NotForSale',
                ],
            'ExpressionAttributeValues' =>
                [
                    ':countries_0' =>
                        [
                            'L' =>
                                [
                                    [
                                        'S' => 'USA',
                                    ],
                                    [
                                        'S' => 'UK',
                                    ],
                                ],
                        ],
                    ':categories_0' =>
                        [
                            'L' =>
                                [
                                    [
                                        'S' => 'IT',
                                    ],
                                    [
                                        'S' => 'AI',
                                    ],
                                    [
                                        'S' => 'Space',
                                    ],
                                ],
                        ],
                    ':price_0' =>
                        [
                            'N' => '20',
                        ],
                    ':color_0' =>
                        [
                            'S' => 'Red',
                        ],
                ],
            'Key' =>
                [
                    'BookId' =>
                        [
                            'S' => 'book-id-for-delete',
                        ],
                    'ReleaseDate' =>
                        [
                            'S' => 'release-date',
                        ],
                ],
        ], $builder->getQuery());
    }
}
