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
    protected array $facadesSingletons = [];

    public function __construct(
        protected readonly DynamoDbClient $client,
        protected readonly Marshaler $marshaler = new Marshaler(),
        protected readonly ?TableInterface $singleTable = null,
    ) {
    }

    public function query(): QueryFacade
    {
        if (isset($this->facadesSingletons[QueryFacade::class])) {
            $this->facadesSingletons[QueryFacade::class] = new QueryFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[QueryFacade::class];
    }

    public function getItem(): GetItemFacade
    {
        if (isset($this->facadesSingletons[GetItemFacade::class])) {
            $this->facadesSingletons[GetItemFacade::class] = new GetItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[GetItemFacade::class];
    }

    public function deleteItem(): DeleteItemFacade
    {
//        return new DeleteItemFacade(
//            client: $this->client,
//            marshaler: $this->marshaler,
//            defaultTable: $this->singleTable,
//        );

        if (isset($this->facadesSingletons[DeleteItemFacade::class])) {
            $this->facadesSingletons[DeleteItemFacade::class] = new DeleteItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[DeleteItemFacade::class];
    }

    public function updateItem(): UpdateItemFacade
    {
        if (isset($this->facadesSingletons[UpdateItemFacade::class])) {
            $this->facadesSingletons[UpdateItemFacade::class] = new UpdateItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[UpdateItemFacade::class];
    }

    public function putItem(): PutItemFacade
    {
        if (isset($this->facadesSingletons[PutItemFacade::class])) {
            $this->facadesSingletons[PutItemFacade::class] = new PutItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[PutItemFacade::class];
    }

    public function transactGetItems(): TransactGetItemsFacade
    {
        if (isset($this->facadesSingletons[TransactGetItemsFacade::class])) {
            $this->facadesSingletons[TransactGetItemsFacade::class] = new TransactGetItemsFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[TransactGetItemsFacade::class];
    }

    public function transactWriteItems(): TransactWriteItemsFacade
    {
        if (isset($this->facadesSingletons[TransactWriteItemsFacade::class])) {
            $this->facadesSingletons[TransactWriteItemsFacade::class] = new TransactWriteItemsFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[TransactWriteItemsFacade::class];
    }

    public function batchGetItem(): BatchGetItemFacade
    {
        if (isset($this->facadesSingletons[BatchGetItemFacade::class])) {
            $this->facadesSingletons[BatchGetItemFacade::class] = new BatchGetItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[BatchGetItemFacade::class];
    }

    public function batchWriteItem(): BatchWriteItemFacade
    {
        if (isset($this->facadesSingletons[BatchWriteItemFacade::class])) {
            $this->facadesSingletons[BatchWriteItemFacade::class] = new BatchWriteItemFacade(
                client: $this->client,
                marshaler: $this->marshaler,
                defaultTable: $this->singleTable,
            );
        }

        return $this->facadesSingletons[BatchWriteItemFacade::class];
    }
}
