<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use Aws\DynamoDb\SetValue;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Builder;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Dispatchers\BatchGetItem;
use Terseq\Dispatchers\Results\BatchGetItemResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;
use Terseq\Tests\Helpers\DispatcherTestHelper;

#[CoversClass(BatchGetItem::class)]
#[UsesClass(Builder::class)]
#[CoversClass(BatchGetItemResult::class)]
#[UsesClass(SingleWriteOperations::class)]
final class BatchGetItemTest extends TestCase
{
    use DispatcherTestHelper;

    /**
     * @throws JsonException|Exception
     */
    public function testDispatch(): void
    {
        $client = $this->createStub(DynamoDbClientMock::class);

        $client->method('batchGetItem')
            ->willReturn($this->createResult($this->getResponseJson()));

        $dispatcher = new BatchGetItem($client);
        $builder = $this->createStub(\Terseq\Builders\BatchGetItem::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->dispatch($builder);

        $this->checkResult($result);
    }

    public function testDispatchAsync(): void
    {
        $client = $this->getMockBuilder(DynamoDbClientMock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['batchGetItemAsync'])
            ->getMock();

        $promise = $this->createPromise($this->createResult($this->getResponseJson()));

        $client
            ->expects($this->once())
            ->method('batchGetItemAsync')
            ->willReturn($promise);

        $dispatcher = new BatchGetItem($client);
        $builder = $this->createStub(\Terseq\Builders\BatchGetItem::class);
        $builder->method('getQuery')->willReturn([]);

        $promise = $dispatcher->async($builder);

        $this->checkResult($promise->wait());
    }

    protected function checkResult($result): void
    {
        $this->assertInstanceOf(BatchGetItemResult::class, $result);
        $this->assertEquals(
            [
                'Forum' => [
                    [
                        'Name' => 'Amazon DynamoDB',
                        'Threads' => 5,
                        'Messages' => 19,
                        'Views' => 35,
                    ],
                    [
                        'Name' => 'Amazon RDS',
                        'Threads' => 8,
                        'Messages' => 32,
                        'Views' => 38,
                    ],
                    [
                        'Name' => 'Amazon Redshift',
                        'Threads' => 12,
                        'Messages' => 55,
                        'Views' => 47,
                    ],
                ],
                'Thread' => [
                    [
                        'Tags' => new SetValue(['Reads', 'MultipleUsers']),
                        'Message' => 'How many users can read a single data item at a time? Are there any limits?',
                    ],
                ],
            ],
            $result->getResponses(),
        );

        $this->assertEquals(
            [
                'Forum' => [
                    [
                        'ConsistentRead' => true,
                        'Keys' => [
                            [
                                'Name' => 'Amazon ElastiCache',
                                'Category' => 'Amazon Web Services',
                            ],
                        ],
                    ],
                ],
                'Thread' => [
                    [
                        'Keys' => [
                            [
                                'ForumName' => 'Amazon DynamoDB',
                                'Subject' => 'How do I update multiple items?',
                            ],
                        ],
                    ],
                ],
            ],
            $result->getUnprocessedKeys(),
        );

        $this->assertEquals(
            [
                [
                    'TableName' => 'Forum',
                    'CapacityUnits' => 3,
                ],
                [
                    'TableName' => 'Thread',
                    'CapacityUnits' => 1,
                ],
            ],
            $result->getConsumedCapacity(),
        );
    }

    protected function getResponseJson(): string
    {
        return '{
            "Responses": {
                "Forum": [
                    {
                        "Name":{
                            "S":"Amazon DynamoDB"
                        },
                        "Threads":{
                            "N":"5"
                        },
                        "Messages":{
                            "N":"19"
                        },
                        "Views":{
                            "N":"35"
                        }
                    },
                    {
                        "Name":{
                            "S":"Amazon RDS"
                        },
                        "Threads":{
                            "N":"8"
                        },
                        "Messages":{
                            "N":"32"
                        },
                        "Views":{
                            "N":"38"
                        }
                    },
                    {
                        "Name":{
                            "S":"Amazon Redshift"
                        },
                        "Threads":{
                            "N":"12"
                        },
                        "Messages":{
                            "N":"55"
                        },
                        "Views":{
                            "N":"47"
                        }
                    }
                ],
                "Thread": [
                    {
                        "Tags":{
                            "SS":["Reads","MultipleUsers"]
                        },
                        "Message":{
                            "S":"How many users can read a single data item at a time? Are there any limits?"
                        }
                    }
                ]
            },
            "UnprocessedKeys": {
                "Forum": [
                    {
                        "ConsistentRead": true,
                        "Keys": [
                            {
                                "Name": {
                                    "S": "Amazon ElastiCache"
                                },
                                "Category": {
                                    "S": "Amazon Web Services"
                                }
                            }
                        ]
                    }
                ],
                "Thread": [
                    {
                        "Keys": [
                            {
                                "ForumName": {
                                    "S": "Amazon DynamoDB"
                                },
                                "Subject": {
                                    "S": "How do I update multiple items?"
                                }
                            }
                        ]
                    }
                ]
            },
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
