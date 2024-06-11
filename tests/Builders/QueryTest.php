<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Expressions\Condition\Condition;
use Terseq\Builders\Expressions\Condition\ConditionItem;
use Terseq\Builders\Expressions\Condition\GroupCondition;
use Terseq\Builders\Expressions\FilterExpression;
use Terseq\Builders\Keys;
use Terseq\Builders\Operations\Query\Enums\Select;
use Terseq\Builders\Operations\Query\SortKeyCondition;
use Terseq\Builders\Query;
use Terseq\Builders\Shared\Enums\ReturnConsumedCapacity;
use Terseq\Builders\Shared\ValuesStorage;
use Terseq\Builders\Table;
use Terseq\Tests\Fixtures\BooksTable;

#[CoversClass(Query::class)]
#[UsesClass(ValuesStorage::class)]
#[UsesClass(Table::class)]
#[UsesClass(Keys::class)]
#[UsesClass(FilterExpression::class)]
#[UsesClass(SortKeyCondition::class)]
#[UsesClass(GroupCondition::class)]
#[UsesClass(ConditionItem::class)]
#[UsesClass(Condition::class)]
class QueryTest extends TestCase
{
    public function testFullQuery(): void
    {
        $builder = (new Query())
            ->table(new BooksTable())
            ->select(Select::ALL_ATTRIBUTES)
            ->pk('my-book-id')
            ->sk(fn (SortKeyCondition $sk) => $sk->between('2024-01-01', '2024-02-02'))
            ->setConsistentRead(true)
            ->returnConsumedCapacity(ReturnConsumedCapacity::None)
            ->scanIndexForward()
            ->limit(100)
            ->projectionExpression(['Author', 'Title'])
            ->exclusiveStartKey([
                'BookId' => 'my-book-id',
                'ReleaseDate' => '2024-01-01',
            ])
            ->filter(
                fn (FilterExpression $filter) => $filter
                ->equal('Author', 'John Doe')
                ->attributeExists('Title')
                ->group(
                    fn (FilterExpression $filter) => $filter
                    ->equal('Genre', 'Fantasy')
                    ->attributeExists('Pages'),
                )
                ->between('price', 20, 30, type: 'OR'),
            );

        $this->assertEquals([
            'TableName' => 'Books',
            'ExpressionAttributeNames' => [
                '#Author' => 'Author',
                '#Title' => 'Title',
                '#BookId' => 'BookId',
                '#Genre' => 'Genre',
                '#Pages' => 'Pages',
                '#ReleaseDate' => 'ReleaseDate',
                '#price' => 'price',
            ],
            'ProjectionExpression' => '#Author, #Title',
            'ConsistentRead' => true,
            'ReturnConsumedCapacity' => 'NONE',
            'KeyConditionExpression' => '#BookId = :bookid_0 AND #ReleaseDate BETWEEN :releasedate_0 AND :releasedate_1',
            'FilterExpression' => '#Author = :author_0 AND attribute_exists(#Title) AND (#Genre = :genre_0 AND attribute_exists(#Pages)) OR #price BETWEEN :price_0 AND :price_1',
            'ExpressionAttributeValues' => [
                ':author_0' => ['S' => 'John Doe'],
                ':genre_0' => ['S' => 'Fantasy'],
                ':price_0' => ['N' => '20'],
                ':price_1' => ['N' => '30'],
                ':bookid_0' => ['S' => 'my-book-id'],
                ':releasedate_0' => ['S' => '2024-01-01'],
                ':releasedate_1' => ['S' => '2024-02-02'],
            ],
            'ScanIndexForward' => true,
            'Limit' => 100,
            'ExclusiveStartKey' => [
                'BookId' => ['S' => 'my-book-id'],
                'ReleaseDate' => ['S' => '2024-01-01'],
            ],
            'Select' => 'ALL_ATTRIBUTES',
        ], $builder->getQuery());
    }

    public function testComposite(): void
    {
        $builder = (new Query())
            ->table(new BooksTable())
            ->composite('pk-id', 'sk-id');

        $this->assertEquals([
            'TableName' => 'Books',
            'KeyConditionExpression' => '#BookId = :bookid_0 AND #ReleaseDate = :releasedate_0',
            'ExpressionAttributeNames' => [
                '#BookId' => 'BookId',
                '#ReleaseDate' => 'ReleaseDate',
            ],
            'ExpressionAttributeValues' => [
                ':bookid_0' => ['S' => 'pk-id'],
                ':releasedate_0' => ['S' => 'sk-id'],
            ],
        ], $builder->getQuery());
    }

    public function testSecondayIndex(): void
    {
        $builder = (new Query())
            ->table(new BooksTable())
            ->secondaryIndex('Author')
            ->composite('gsi-pk', 'gsi-sk');

        $this->assertEquals([
            'TableName' => 'Books',
            'IndexName' => 'Author',
            'KeyConditionExpression' => '#AuthorId = :authorid_0 AND #BornDate = :borndate_0',
            'ExpressionAttributeNames' => [
                '#AuthorId' => 'AuthorId',
                '#BornDate' => 'BornDate',
            ],
            'ExpressionAttributeValues' => [
                ':authorid_0' => ['S' => 'gsi-pk'],
                ':borndate_0' => ['S' => 'gsi-sk'],
            ],
        ], $builder->getQuery());
    }

    public function testKeysWasPassedManually(): void
    {
        $builder = (new Query())
            ->table(new BooksTable())
            ->secondaryIndex('TEST')
            ->notScanIndexForward()
            ->composite('pk-id', 'sk-id', 'MyPk', 'MySk');
        ;

        $this->assertEquals([
            'TableName' => 'Books',
            'IndexName' => 'TEST',
            'KeyConditionExpression' => '#MyPk = :mypk_0 AND #MySk = :mysk_0',
            'ExpressionAttributeNames' => [
                '#MyPk' => 'MyPk',
                '#MySk' => 'MySk',
            ],
            'ExpressionAttributeValues' => [
                ':mypk_0' => ['S' => 'pk-id'],
                ':mysk_0' => ['S' => 'sk-id'],
            ],
            'ScanIndexForward' => false,
        ], $builder->getQuery());
    }

    public function testWithoutPartitionKey(): void
    {
        $builder = (new Query())
            ->table(new BooksTable());

        $this->expectException(BuilderException::class);
        $builder->getQuery();
    }
}
