<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\PutItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Operations\PutItem\PutItem;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Enums\ReturnItemCollectionMetrics;
use Terseq\Builders\Shared\Enums\ReturnValues;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(PutItem::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnValues::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics::class)]
#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity::class)]
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
        ],
            (new PutItem())
            ->table(new BooksTable())
            ->returnConsumedCapacity(ReturnConsumedCapacity::Indexes)
            ->returnItemCollectionMetrics(ReturnItemCollectionMetrics::Size)
            ->returnValues(ReturnValues::AllOld)
            ->returnValuesOnConditionCheckFailure(ReturnValues::AllNew)
            ->item([
                'BookId' => 'book-id-for-delete',
                'ReleaseDate' => 'release-date',
            ])
            ->getQuery()
        );
    }
}
