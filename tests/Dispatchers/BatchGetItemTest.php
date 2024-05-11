<?php

declare(strict_types=1);

namespace Terseq\Tests\Dispatchers;

use Aws\DynamoDb\Marshaler;
use Aws\Result;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Builder;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Dispatchers\BatchGetItem;
use Terseq\Dispatchers\Results\BatchGetItemResult;
use Terseq\Tests\Fixtures\DynamoDbClientMock;

#[CoversClass(BatchGetItem::class)]
#[UsesClass(Builder::class)]
#[UsesClass(BatchGetItemResult::class)]
#[UsesClass(SingleWriteOperations::class)]
final class BatchGetItemTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testDispatch(): void
    {
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


        $dispatcher = new BatchGetItem($client);
        $builder = $this->createStub(\Terseq\Builders\BatchGetItem::class);
        $builder->method('getQuery')->willReturn([]);

        $result = $dispatcher->dispatch($builder);

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
