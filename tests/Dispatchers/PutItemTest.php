<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use Aws\DynamoDb\SetValue;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Terseq\Dispatchers\PutItem;
use Terseq\Dispatchers\Results\Helpers\ConvertItemCollectionMetrics;
use Terseq\Dispatchers\Results\WriteOperationResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;
use Terseq\Tests\Helpers\DispatcherTestHelper;

#[CoversClass(PutItem::class)]
#[CoversClass(WriteOperationResult::class)]
#[CoversClass(ConvertItemCollectionMetrics::class)]
class PutItemTest extends TestCase
{
    use DispatcherTestHelper;

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testDispatch(): void
    {
        $client = $this->createStub(DynamoDbClientMock::class);

        $client->method('putItem')
            ->willReturn($this->createResult($this->getResponseJson()));

        $dispatcher = new PutItem($client);
        $builder = $this->createStub(\Terseq\Builders\PutItem::class);
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
            ->onlyMethods(['putItemAsync'])
            ->getMock();

        $promise = $this->createPromise($this->createResult($this->getResponseJson()));

        $client
            ->expects($this->once())
            ->method('putItemAsync')
            ->willReturn($promise);

        $dispatcher = new PutItem($client);
        $builder = $this->createStub(\Terseq\Builders\PutItem::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->async($builder)->wait();

        $this->checkResult($result);
    }

    protected function checkResult($result): void
    {
        $this->assertInstanceOf(
            WriteOperationResult::class,
            $result,
        );

        $this->assertEquals([
            'LastPostedBy' => 'fred@example.com',
            'ForumName' => 'Amazon DynamoDB',
            'LastPostDateTime' => '201303201023',
            'Tags' => new SetValue(['Update', 'Multiple Items', 'HelpMe']),
            'Subject' => 'How do I update multiple items?',
            'Message' => 'I want to update multiple items in a single call. What\'s the best way to do that?',
        ], $result->getAttributes());

        $this->assertEquals(null, $result->getConsumedCapacity());
        $this->assertEquals([
            'ItemCollectionKey' => [
                'ForumName' => 'Amazon DynamoDB',
                'Subject' => 'How do I update multiple items?',
            ],
            'SizeEstimateRangeGB' => [1.0, 1.5],
        ], $result->getItemCollectionMetrics());
    }

    protected function getResponseJson(): string
    {
        return '{
            "Attributes": {
                "LastPostedBy": {
                    "S": "fred@example.com"
                },
                "ForumName": {
                    "S": "Amazon DynamoDB"
                },
                "LastPostDateTime": {
                    "S": "201303201023"
                },
                "Tags": {
                    "SS": ["Update","Multiple Items","HelpMe"]
                },
                "Subject": {
                    "S": "How do I update multiple items?"
                },
                "Message": {
                    "S": "I want to update multiple items in a single call. What\'s the best way to do that?"
                }
            },
            "ItemCollectionMetrics": {
                "ItemCollectionKey": {
                    "ForumName": {
                        "S": "Amazon DynamoDB"
                    },
                    "Subject": {
                        "S": "How do I update multiple items?"
                    }
                },
                "SizeEstimateRangeGB": [1.0, 1.5]
            }
        }';
    }
}
