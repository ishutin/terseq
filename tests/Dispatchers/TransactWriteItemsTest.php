<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Dispatchers\Results\Helpers\Transact\ConvertMultiplyItemCollectionMetrics;
use Terseq\Dispatchers\Results\TransactWriteItemsResult;
use Terseq\Dispatchers\TransactWriteItems;
use Terseq\Tests\Fixtures\DynamoDbClientMock;
use Terseq\Tests\Helpers\DispatcherTestHelper;

#[CoversClass(TransactWriteItems::class)]
#[UsesClass(TransactWriteItemsResult::class)]
#[UsesClass(ConvertMultiplyItemCollectionMetrics::class)]
class TransactWriteItemsTest extends TestCase
{
    use DispatcherTestHelper;


    public function testDispatch(): void
    {
        $client = $this->createStub(DynamoDbClientMock::class);

        $client->method('transactWriteItems')
            ->willReturn($this->createResult($this->getResponseJson()));

        $dispatcher = new TransactWriteItems($client);
        $builder = $this->createStub(\Terseq\Builders\TransactWriteItems::class);
        $builder->method('getQuery')->willReturn([]);

        $response = $dispatcher->dispatch($builder);

        $this->checkResponse($response);
    }

    public function testDispatchAsync(): void
    {
        $client = $this->getMockBuilder(DynamoDbClientMock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['transactWriteItemsAsync'])
            ->getMock();

        $promise = $this->createPromise($this->createResult($this->getResponseJson()));

        $client
            ->expects($this->once())
            ->method('transactWriteItemsAsync')
            ->willReturn($promise);

        $dispatcher = new TransactWriteItems($client);
        $builder = $this->createStub(\Terseq\Builders\TransactWriteItems::class);
        $builder->method('getQuery')->willReturn([]);

        $response = $dispatcher->async($builder)->wait();

        $this->checkResponse($response);
    }

    protected function checkResponse($response): void
    {
        $this->assertInstanceOf(TransactWriteItemsResult::class, $response);

        $this->assertEquals(
            [
                [
                    'TableName' => 'Forum',
                    'CapacityUnits' => 3,
                ],
            ],
            $response->getConsumedCapacity(),
        );
    }

    protected function getResponseJson(): string
    {
        return '{
            "ConsumedCapacity": [
                {
                    "TableName": "Forum",
                    "CapacityUnits": 3
                }
            ]
        }';
    }
}
