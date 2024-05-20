<?php

declare(strict_types=1);

namespace Terseq\Tests;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Builder;
use Terseq\Contracts\Builder\TableInterface;
use Terseq\DatabaseManager;
use Terseq\Dispatchers\Dispatcher;
use Terseq\Essentials\BatchGetItem;
use Terseq\Essentials\BatchWriteItem;
use Terseq\Essentials\DeleteItem;
use Terseq\Essentials\Essential;
use Terseq\Essentials\GetItem;
use Terseq\Essentials\PutItem;
use Terseq\Essentials\Query;
use Terseq\Essentials\TransactGetItems;
use Terseq\Essentials\TransactWriteItems;
use Terseq\Essentials\UpdateItem;

#[CoversClass(DatabaseManager::class)]
#[UsesClass(Dispatcher::class)]
#[UsesClass(Essential::class)]
#[UsesClass(Builder::class)]
final class DatabaseManagerTest extends TestCase
{
    private DynamoDbClient $client;
    private Marshaler $marshaler;
    private TableInterface $table;
    private DatabaseManager $databaseManager;

    protected function setUp(): void
    {
        $this->client = $this->createMock(DynamoDbClient::class);
        $this->marshaler = $this->createMock(Marshaler::class);
        $this->table = $this->createMock(TableInterface::class);
        $this->databaseManager = new DatabaseManager($this->client, $this->marshaler, $this->table);
    }

    public function testQuery(): void
    {
        $result = $this->databaseManager->query();
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testGetItem(): void
    {
        $result = $this->databaseManager->getItem();
        $this->assertInstanceOf(GetItem::class, $result);
    }

    public function testDeleteItem(): void
    {
        $result = $this->databaseManager->deleteItem();
        $this->assertInstanceOf(DeleteItem::class, $result);
    }

    public function testUpdateItem(): void
    {
        $result = $this->databaseManager->updateItem();
        $this->assertInstanceOf(UpdateItem::class, $result);
    }

    public function testPutItem(): void
    {
        $result = $this->databaseManager->putItem();
        $this->assertInstanceOf(PutItem::class, $result);
    }

    public function testTransactGetItems(): void
    {
        $result = $this->databaseManager->transactGetItems();
        $this->assertInstanceOf(TransactGetItems::class, $result);
    }

    public function testTransactWriteItems(): void
    {
        $result = $this->databaseManager->transactWriteItems();
        $this->assertInstanceOf(TransactWriteItems::class, $result);
    }

    public function testBatchGetItem(): void
    {
        $result = $this->databaseManager->batchGetItem();
        $this->assertInstanceOf(BatchGetItem::class, $result);
    }

    public function testBatchWriteItem(): void
    {
        $result = $this->databaseManager->batchWriteItem();
        $this->assertInstanceOf(BatchWriteItem::class, $result);
    }
}
