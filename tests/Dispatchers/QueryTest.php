<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use loophp\collection\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Casters\Caster;
use Terseq\Dispatchers\Query;
use Terseq\Dispatchers\Results\QueryResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;
use Terseq\Tests\Helpers\DispatcherTestHelper;

#[CoversClass(Query::class)]
#[UsesClass(QueryResult::class)]
#[UsesClass(Caster::class)]
class QueryTest extends TestCase
{
    use DispatcherTestHelper;

    public function testDispatch(): void
    {
        $client = $this->createStub(DynamoDbClientMock::class);

        $client->method('query')
            ->willReturn($this->createResult($this->getResponseJson()));

        $dispatcher = new Query($client);
        $builder = $this->createStub(\Terseq\Builders\Query::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->dispatch($builder);
        $this->checkResult($result);
    }

    public function testDispatchAsync(): void
    {
        $client = $this->getMockBuilder(DynamoDbClientMock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['queryAsync'])
            ->getMock();

        $promise = $this->createPromise($this->createResult($this->getResponseJson()));

        $client
            ->expects($this->once())
            ->method('queryAsync')
            ->willReturn($promise);

        $dispatcher = new Query($client);
        $builder = $this->createStub(\Terseq\Builders\Query::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->async($builder)->wait();
        $this->checkResult($result);
    }

    protected function checkResult($result): void
    {
        $this->assertInstanceOf(\Terseq\Dispatchers\Results\QueryResult::class, $result);
        $this->assertEquals(2, $result->getCount());
        $this->assertEquals(2, $result->getScannedCount());
        $this->assertInstanceOf(Collection::class, $result->getItems());
        $this->assertEquals(
            [
                [
                    'ReplyDateTime' => '2015-02-18T20:27:36.165Z',
                    'PostedBy' => 'User A',
                    'Id' => 'Amazon DynamoDB#DynamoDB Thread 1',
                ],
                [
                    'ReplyDateTime' => '2015-02-25T20:27:36.165Z',
                    'PostedBy' => 'User B',
                    'Id' => 'Amazon DynamoDB#DynamoDB Thread 1',
                ],
            ],
            $result->getItems()->all(),
        );

        $this->assertEquals(
            [
                'CapacityUnits' => 1,
                'TableName' => 'Reply',
            ],
            $result->getConsumedCapacity(),
        );
    }

    protected function getResponseJson(): string
    {
        return ' {
            "ConsumedCapacity": {
                "CapacityUnits": 1,
                "TableName": "Reply"
            },
            "Count": 2,
            "Items": [
                {
                    "ReplyDateTime": {"S": "2015-02-18T20:27:36.165Z"},
                    "PostedBy": {"S": "User A"},
                    "Id": {"S": "Amazon DynamoDB#DynamoDB Thread 1"}
                },
                {
                    "ReplyDateTime": {"S": "2015-02-25T20:27:36.165Z"},
                    "PostedBy": {"S": "User B"},
                    "Id": {"S": "Amazon DynamoDB#DynamoDB Thread 1"}
                }
            ],
            "ScannedCount": 2
        }';
    }
}
