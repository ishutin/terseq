<?php

declare(strict_types=1);

namespace Terseq;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Terseq\Contracts\Builder\TableInterface;
use Terseq\Contracts\DatabaseManagerInterface;
use Terseq\Facades\BatchGetItemFacade;
use Terseq\Facades\BatchWriteItemFacade;
use Terseq\Facades\DeleteItemFacade;
use Terseq\Facades\GetItemFacade;
use Terseq\Facades\PutItemFacade;
use Terseq\Facades\QueryFacade;
use Terseq\Facades\TransactGetItemsFacade;
use Terseq\Facades\TransactWriteItemsFacade;
use Terseq\Facades\UpdateItemFacade;

class DatabaseManager implements DatabaseManagerInterface
{
    protected array $instances = [];

    public function __construct(
        protected readonly DynamoDbClient $client,
        protected readonly Marshaler $marshaler = new Marshaler(),
        protected readonly ?TableInterface $singleTable = null,
    ) {
    }

    public function query(): QueryFacade
    {
        if (!isset($this->instances[QueryFacade::class])) {
            $this->instances[QueryFacade::class] = new QueryFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[QueryFacade::class];
    }

    public function getItem(): GetItemFacade
    {
        if (!isset($this->instances[GetItemFacade::class])) {
            $this->instances[GetItemFacade::class] = new GetItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[GetItemFacade::class];
    }

    public function deleteItem(): DeleteItemFacade
    {
        if (!isset($this->instances[DeleteItemFacade::class])) {
            $this->instances[DeleteItemFacade::class] = new DeleteItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[DeleteItemFacade::class];
    }

    public function updateItem(): UpdateItemFacade
    {
        if (!isset($this->instances[UpdateItemFacade::class])) {
            $this->instances[UpdateItemFacade::class] = new UpdateItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[UpdateItemFacade::class];
    }

    public function putItem(): PutItemFacade
    {
        if (!isset($this->instances[PutItemFacade::class])) {
            $this->instances[PutItemFacade::class] = new PutItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[PutItemFacade::class];
    }

    public function transactGetItems(): TransactGetItemsFacade
    {
        if (!isset($this->instances[TransactGetItemsFacade::class])) {
            $this->instances[TransactGetItemsFacade::class] = new TransactGetItemsFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[TransactGetItemsFacade::class];
    }

    public function transactWriteItems(): TransactWriteItemsFacade
    {
        if (!isset($this->instances[TransactWriteItemsFacade::class])) {
            $this->instances[TransactWriteItemsFacade::class] = new TransactWriteItemsFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[TransactWriteItemsFacade::class];
    }

    public function batchGetItem(): BatchGetItemFacade
    {
        if (!isset($this->instances[BatchGetItemFacade::class])) {
            $this->instances[BatchGetItemFacade::class] = new BatchGetItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[BatchGetItemFacade::class];
    }

    public function batchWriteItem(): BatchWriteItemFacade
    {
        if (!isset($this->instances[BatchWriteItemFacade::class])) {
            $this->instances[BatchWriteItemFacade::class] = new BatchWriteItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->instances[BatchWriteItemFacade::class];
    }
}
