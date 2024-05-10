<?php

declare(strict_types=1);

namespace Terseq\Tests\Facades;

use Aws\DynamoDb\Marshaler;
use Aws\Result;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Dispatchers\BatchGetItem;
use Terseq\Dispatchers\Results\BatchGetItemResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;

#[CoversClass(BatchGetItem::class)]
final class BatchGetItemFacadeTest extends TestCase
{
    public function testDispatch(): void
    {
        //        $client = $this->createMock(DynamoDbClient::class);
        $client = $this->getMockBuilder(DynamoDbClientMock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['batchGetItem'])
            ->getMock();

        $client->expects($this->once())
            ->method('batchGetItem')->willReturn(
                new Result([
                    '@metadata' => [
                        'metadata' => 'test',
                    ],
                    'Responses' => [
                        [
                            (new Marshaler())->marshalItem([
                                'Id' => 'id',
                                'Name' => 'name',
                                'Banned' => false,
                                'Age' => 25,
                            ]),
                        ],
                        [
                            (new Marshaler())->marshalItem([
                                'Id' => 'id2',
                                'Name' => 'test',
                                'Banned' => true,
                                'Age' => 44,
                            ]),
                        ],
                    ],
                ]),
            );


        $facade = new BatchGetItem($client);
        $builder = new \Terseq\Builders\BatchGetItem('testTable');

        $result = $facade->dispatch($builder);

        $this->assertInstanceOf(BatchGetItemResult::class, $result);
        $this->assertEquals(
            [
                [
                    'Id' => 'id',
                    'Name' => 'name',
                    'Banned' => false,
                    'Age' => 25,
                ],
                [
                    'Id' => 'id2',
                    'Name' => 'test',
                    'Banned' => true,
                    'Age' => 44,
                ],
            ],
            $result->responses,
        );
    }
}
