<?php

declare(strict_types=1);

namespace Terseq;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Terseq\Contracts\Builder\TableInterface;
use Terseq\Contracts\DatabaseManagerInterface;
use Terseq\Dispatchers\Dispatcher;
use Terseq\Essentials\DeleteItem;

class DatabaseManager implements DatabaseManagerInterface
{
    protected array $dispatcherInstances = [];

    public function __construct(
        protected readonly DynamoDbClient $client,
        protected readonly Marshaler $marshaler = new Marshaler(),
        protected readonly ?TableInterface $singleTable = null,
    ) {
    }

    public function query(): Essentials\Query
    {
        return (new Essentials\Query(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\Query::class));
    }

    public function getITem(): Essentials\GetItem
    {
        return (new Essentials\GetItem(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\GetItem::class));
    }

    public function deleteItem(): DeleteItem
    {
        return (new Essentials\DeleteItem(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\DeleteItem::class));
    }

    public function updateItem(): Essentials\UpdateItem
    {
        return (new Essentials\UpdateItem(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\UpdateItem::class));
    }

    public function putItem(): Essentials\PutItem
    {
        return (new Essentials\PutItem(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\PutItem::class));
    }

    public function transactGetItems(): Essentials\TransactGetItems
    {
        return (new Essentials\TransactGetItems(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\TransactGetItems::class));
    }

    public function transactWriteItems(): Essentials\TransactWriteItems
    {
        return (new Essentials\TransactWriteItems(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\TransactWriteItems::class));
    }

    public function batchGetItem(): Essentials\BatchGetItem
    {
        return (new Essentials\BatchGetItem(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\BatchGetItem::class));
    }

    public function batchWriteItem(): Essentials\BatchWriteItem
    {
        return (new Essentials\BatchWriteItem(
            table: $this->singleTable,
            marshaler: $this->marshaler,
        ))->setDispatcher($this->getDispatcher(Dispatchers\BatchWriteItem::class));
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T & Dispatcher
     */
    protected function getDispatcher(string $className): Dispatcher
    {
        if (!isset($this->dispatcherInstances[$className])) {
            $this->dispatcherInstances[$className] = new $className(
                client: $this->client,
                marshaler: $this->marshaler,
            );
        }

        return $this->dispatcherInstances[$className];
    }
}
