<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use Aws\DynamoDb\SetValue;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Casters\Caster;
use Terseq\Dispatchers\GetItem;
use Terseq\Dispatchers\Results\GetItemResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;
use Terseq\Tests\Helpers\DispatcherTestHelper;

#[CoversClass(GetItem::class)]
#[UsesClass(GetItemResult::class)]
#[UsesClass(Caster::class)]
class GetItemTest extends TestCase
{
    use DispatcherTestHelper;

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function testDispatch(): void
    {
        $client = $this->createStub(DynamoDbClientMock::class);

        $client->method('getItem')
            ->willReturn($this->createResult($this->getResponseJson()));

        $dispatcher = new GetItem($client);
        $builder = $this->createStub(\Terseq\Builders\GetItem::class);
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
            ->onlyMethods(['getItemAsync'])
            ->getMock();

        $promise = $this->createPromise($this->createResult($this->getResponseJson()));

        $client
            ->expects($this->once())
            ->method('getItemAsync')
            ->willReturn($promise);

        $dispatcher = new GetItem($client);
        $builder = $this->createStub(\Terseq\Builders\GetItem::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->async($builder)->wait();
        $this->checkResult($result);
    }

    protected function checkResult($result): void
    {
        $this->assertInstanceOf(GetItemResult::class, $result);

        $this->assertEquals(
            [
                'CapacityUnits' => 1,
                'TableName' => 'Thread',
            ],
            $result->getConsumedCapacity(),
        );

        $this->assertEquals(
            [
                'Tags' => new SetValue(['Update', 'Multiple Items', 'HelpMe']),
                'LastPostDateTime' => '201303190436',
                'Message' => 'I want to update multiple items in a single call. What\'s the best way to do that ? ',
            ],
            $result->getItem(),
        );
    }

    protected function getResponseJson(): string
    {
        return '{
            "ConsumedCapacity": {
                "CapacityUnits": 1,
                "TableName": "Thread"
            },
            "Item": {
                "Tags": {
                    "SS": ["Update","Multiple Items","HelpMe"]
                },
                "LastPostDateTime": {
                    "S": "201303190436"
                },
                "Message": {
                    "S": "I want to update multiple items in a single call. What\'s the best way to do that ? "
                }
            }
        }';
    }
}
