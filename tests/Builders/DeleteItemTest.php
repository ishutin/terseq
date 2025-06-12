<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use Terseq\Builders\Keys;
use Terseq\Builders\Table;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\DeleteItem;
use Terseq\Tests\Fixtures\BooksTable;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;
use Terseq\Builders\Shared\Enums\ReturnValues;
use Terseq\Builders\Expressions\ConditionExpression;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Enums\ReturnItemCollectionMetrics;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;

#[CoversClass(DeleteItem::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
#[UsesClass(\Terseq\Builders\Expressions\Condition\Condition::class)]
#[UsesClass(\Terseq\Builders\Expressions\Condition\ConditionItem::class)]
#[UsesClass(\Terseq\Builders\Expressions\Expression::class)]
#[UsesClass(\Terseq\Builders\Expressions\FilterExpression::class)]
#[UsesClass(\Terseq\Builders\Shared\Extends\RenderCondition::class)]
#[UsesClass(\Terseq\Builders\Shared\ValuesStorage::class)]
#[CoversClass(SingleWriteOperations::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnValues::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\AppendAttributes::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ConditionExpression::class)]
#[CoversClass(ReturnValuesOnConditionCheckFailure::class)]
class DeleteItemTest extends TestCase
{
    public function testFullQuery(): void
    {
        $builder = (new DeleteItem())
            ->table(new BooksTable())
            ->returnConsumedCapacity(ReturnConsumedCapacity::Indexes)
            ->returnItemCollectionMetrics(ReturnItemCollectionMetrics::Size)
            ->returnValues(ReturnValues::AllOld)
            ->returnValuesOnConditionCheckFailure(ReturnValues::AllNew)
            ->conditionExpression(
                static fn (ConditionExpression $ce) => $ce
                    ->attributeExists('BookId')
                    ->equal('ReleaseDate', 'release-date')
            )
            ->composite('book-id-for-delete', 'release-date');

        $this->assertEquals([
            'TableName' => 'Books',
            'ReturnConsumedCapacity' => 'INDEXES',
            'ReturnItemCollectionMetrics' => 'SIZE',
            'ReturnValues' => 'ALL_OLD',
            'ReturnValuesOnConditionCheckFailure' => 'ALL_NEW',
            'Key' => [
                'BookId' => [
                    'S' => 'book-id-for-delete',
                ],
                'ReleaseDate' => [
                    'S' => 'release-date',
                ],
            ],
            'ConditionExpression' => 'attribute_exists(#BookId) AND #ReleaseDate = :releasedate_0',
            'ExpressionAttributeValues' => [
                ':releasedate_0' => [
                    'S' => 'release-date',
                ],
            ],
            'ExpressionAttributeNames' => [
                '#BookId' => 'BookId',
                '#ReleaseDate' => 'ReleaseDate',
            ],
        ], $builder->getQuery());
    }
}
