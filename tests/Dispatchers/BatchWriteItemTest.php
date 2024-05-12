<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Terseq\Dispatchers\BatchWriteItem;
use Terseq\Dispatchers\Results\BatchWriteItemResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;
use Terseq\Tests\Helpers\DispatcherTestHelper;

#[CoversClass(BatchWriteItem::class)]
#[UsesClass(BatchWriteItemResult::class)]
class BatchWriteItemTest extends TestCase
{
    use DispatcherTestHelper;

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testDispatch(): void
    {
        $client = $this->createStub(DynamoDbClientMock::class);

        $client->method('batchWriteItem')
            ->willReturn($this->createResult($this->getResponseJson()));

        $dispatcher = new BatchWriteItem($client);
        $builder = $this->createStub(\Terseq\Builders\BatchWriteItem::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->dispatch($builder);

        $this->checkResult($result);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testDispatchAsync(): void
    {
        $client = $this->getMockBuilder(DynamoDbClientMock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['batchWriteItemAsync'])
            ->getMock();

        $promise = $this->createPromise($this->createResult($this->getResponseJson()));

        $client
            ->expects($this->once())
            ->method('batchWriteItemAsync')
            ->willReturn($promise);

        $dispatcher = new BatchWriteItem($client);
        $builder = $this->createStub(\Terseq\Builders\BatchWriteItem::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->async($builder)->wait();

        $this->checkResult($result);
    }

    protected function checkResult($result): void
    {
        $this->assertInstanceOf(BatchWriteItemResult::class, $result);
        $this->assertEquals([
            'Forum' => [
                [
                    'PutRequest' => [
                        'Item' => [
                            'Name' => 'Amazon ElastiCache',
                            'Category' => 'Amazon Web Services',
                        ],
                    ],
                ],
            ],
        ], $result->getUnprocessedItems());

        $this->assertEquals([
            [
                'TableName' => 'Forum',
                'CapacityUnits' => 3,
            ],
        ], $result->getConsumedCapacity());

        $this->assertNull($result->getItemCollectionMetrics());
    }

    protected function getResponseJson(): string
    {
        return '{
            "UnprocessedItems": {
                "Forum": [
                    {
                        "PutRequest": {
                            "Item": {
                                "Name": {
                                    "S": "Amazon ElastiCache"
                                },
                                "Category": {
                                    "S": "Amazon Web Services"
                                }
                            }
                        }
                    }
                ]
            },
            "ConsumedCapacity": [
                {
                    "TableName": "Forum",
                    "CapacityUnits": 3
                }
            ]
        }';
    }

}
