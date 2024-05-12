<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Dispatchers\Results\Helpers\Transact\ConvertMultiplyItemCollectionMetrics;
use Terseq\Dispatchers\Results\TransactGetItemsResult;
use Terseq\Dispatchers\TransactGetItems;
use Terseq\Tests\Fixtures\DynamoDbClientMock;
use Terseq\Tests\Helpers\DispatcherTestHelper;

#[CoversClass(TransactGetItems::class)]
#[UsesClass(TransactGetItemsResult::class)]
#[UsesClass(ConvertMultiplyItemCollectionMetrics::class)]
class TransactGetItemsTest extends TestCase
{
    use DispatcherTestHelper;

    public function testDispatch(): void
    {
        $client = $this->createStub(DynamoDbClientMock::class);

        $client->method('transactGetItems')
            ->willReturn($this->createResult($this->getResponseJson()));

        $dispatcher = new TransactGetItems($client);
        $builder = $this->createStub(\Terseq\Builders\TransactGetItems::class);
        $builder->method('getQuery')->willReturn([]);

        $response = $dispatcher->dispatch($builder);

        $this->checkResponse($response);
    }

    public function testDispatchAsync(): void
    {
        $client = $this->getMockBuilder(DynamoDbClientMock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['transactGetItemsAsync'])
            ->getMock();

        $promise = $this->createPromise($this->createResult($this->getResponseJson()));

        $client
            ->expects($this->once())
            ->method('transactGetItemsAsync')
            ->willReturn($promise);

        $dispatcher = new TransactGetItems($client);
        $builder = $this->createStub(\Terseq\Builders\TransactGetItems::class);
        $builder->method('getQuery')->willReturn([]);

        $response = $dispatcher->async($builder)->wait();

        $this->checkResponse($response);
    }

    protected function checkResponse($response): void
    {
        $this->assertInstanceOf(TransactGetItemsResult::class, $response);
        $this->assertEquals([
            [
                'Name' => 'Amazon DynamoDB',
                'Threads' => 2,
                'Messages' => 4,
                'Views' => 1000,
            ],
            [
                'Name' => 'Amazon S3',
                'Threads' => 3,
                'Messages' => 6,
                'Views' => 2000,
            ],
            [
                'ForumName' => 'Amazon DynamoDB',
                'Subject' => 'DynamoDB Thread 1',
                'Message' => 'DynamoDB thread 1 message text',
                'Keyword' => 'Amazon DynamoDB',
            ],
        ], $response->getResponses());

        $this->assertEquals([
            [
                'TableName' => 'Forum',
                'CapacityUnits' => 3,
            ],
            [
                'TableName' => 'Thread',
                'CapacityUnits' => 1,
            ],
        ], $response->getConsumedCapacity());

    }

    protected function getResponseJson(): string
    {
        return '{
            "Responses": [
                {
                    "Item": {
                        "Name": {
                            "S": "Amazon DynamoDB"
                        },
                        "Threads": {
                            "N": "2"
                        },
                        "Messages": {
                            "N": "4"
                        },
                        "Views": {
                            "N": "1000"
                        }
                    }
                },
                {
                    "Item": {
                        "Name": {
                            "S": "Amazon S3"
                        },
                        "Threads": {
                            "N": "3"
                        },
                        "Messages": {
                            "N": "6"
                        },
                        "Views": {
                            "N": "2000"
                        }
                    }
                },
                {
                    "Item": {
                        "ForumName": {
                            "S": "Amazon DynamoDB"
                        },
                        "Subject": {
                            "S": "DynamoDB Thread 1"
                        },
                        "Message": {
                            "S": "DynamoDB thread 1 message text"
                        },
                        "Keyword": {
                            "S": "Amazon DynamoDB"
                        }
                    }
                }
            ],
            "ConsumedCapacity": [
                {
                    "TableName": "Forum",
                    "CapacityUnits": 3
                },
                {
                    "TableName": "Thread",
                    "CapacityUnits": 1
                }
            ]
        }';
    }
}
