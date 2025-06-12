<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use Terseq\Builders\PutItem;
use PHPUnit\Framework\TestCase;
use Terseq\Tests\Fixtures\BooksTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Terseq\Builders\Shared\Enums\ReturnValues;
use Terseq\Builders\Expressions\ConditionExpression;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Enums\ReturnItemCollectionMetrics;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;

#[CoversClass(PutItem::class)]
#[UsesClass(\Terseq\Builders\Expressions\Condition\Condition::class)]
#[UsesClass(\Terseq\Builders\Expressions\Condition\ConditionItem::class)]
#[UsesClass(\Terseq\Builders\Expressions\Expression::class)]
#[UsesClass(\Terseq\Builders\Expressions\FilterExpression::class)]
#[UsesClass(\Terseq\Builders\Shared\Extends\RenderCondition::class)]
#[UsesClass(\Terseq\Builders\Shared\ValuesStorage::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnValues::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ConditionExpression::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\AppendAttributes::class)]
#[CoversClass(ReturnValuesOnConditionCheckFailure::class)]
final class PutItemTest extends TestCase
{
    public function testFullQuery(): void
    {
        $this->assertEquals(
            [
            'TableName' => 'Books',
            'ReturnConsumedCapacity' => 'INDEXES',
            'ReturnItemCollectionMetrics' => 'SIZE',
            'ReturnValues' => 'ALL_OLD',
            'ReturnValuesOnConditionCheckFailure' => 'ALL_NEW',
            'Item' => [
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
        ],
            (new PutItem())
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
            ->item([
                'BookId' => 'book-id-for-delete',
                'ReleaseDate' => 'release-date',
            ])
            ->getQuery()
        );
    }
}
