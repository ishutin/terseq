<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Operations\DeleteItem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\DeleteItem\DeleteItem;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Enums\ReturnItemCollectionMetrics;
use Terseq\Builders\Shared\Enums\ReturnValues;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BookTable;

#[CoversClass(DeleteItem::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
//#[CoversClass(SingleWriteOperations::class)]
//#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnValues::class)]
//#[CoversClass(\Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics::class)]
//#[CoversClass(ReturnValuesOnConditionCheckFailure::class)]
class DeleteItem2 extends TestCase
{
    public function not(): void
    {
        $builder = (new DeleteItem(
            table: new BookTable(),
        ))
            ->returnConsumedCapacity(ReturnConsumedCapacity::Indexes)
            ->returnItemCollectionMetrics(ReturnItemCollectionMetrics::Size)
            ->returnValues(ReturnValues::AllOld)
            ->returnValuesOnConditionCheckFailure(ReturnValues::AllNew)
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
        ], $builder->getQuery());
    }
}