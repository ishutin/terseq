<?php

declare(strict_types=1);

namespace Terseq\Tests\Facades;

use Aws\DynamoDb\Marshaler;
use Aws\Result;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Operations\BatchGetItem\BatchGetItem;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Facades\BatchGetItemFacade;
use Terseq\Facades\Results\BatchGetItemResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;

#[CoversClass(BatchGetItemFacade::class)]
#[CoversClass(BatchGetItemResult::class)]
#[UsesClass(BatchGetItem::class)]
#[UsesClass(Builder::class)]
#[UsesClass(SingleWriteOperations::class)]
final class BatchGetItemFacadeTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testMakeBuilder(): void
    {
        $client = $this->createMock(DynamoDbClientMock::class);
        $facade = new BatchGetItemFacade($client);
        $this->assertInstanceOf(BatchGetItem::class, $facade->makeBuilder());
    }

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


        $facade = new BatchGetItemFacade($client);
        $builder = new BatchGetItem('testTable');

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
