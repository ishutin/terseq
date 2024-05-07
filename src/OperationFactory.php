<?php

declare(strict_types=1);

namespace Terseq;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Terseq\Contracts\Builder\TableInterface;
use Terseq\Contracts\OperationFactoryInterface;
use Terseq\Facades\BatchGetItemFacade;
use Terseq\Facades\BatchWriteItemFacade;
use Terseq\Facades\DeleteItemFacade;
use Terseq\Facades\GetItemFacade;
use Terseq\Facades\PutItemFacade;
use Terseq\Facades\QueryFacade;
use Terseq\Facades\TransactGetItemsFacade;
use Terseq\Facades\TransactWriteItemsFacade;
use Terseq\Facades\UpdateItemFacade;

readonly class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        protected DynamoDbClient $client,
        protected Marshaler $marshaler = new Marshaler(),
        protected ?TableInterface $singleTable = null,
    ) {
    }

    public function query(): QueryFacade
    {
        return new QueryFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function getItem(): GetItemFacade
    {
        return new GetItemFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function deleteItem(): DeleteItemFacade
    {
        return new DeleteItemFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function updateItem(): UpdateItemFacade
    {
        return new UpdateItemFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function putItem(): PutItemFacade
    {
        return new PutItemFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function transactGetItems(): TransactGetItemsFacade
    {
        return new TransactGetItemsFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function transactWriteItems(): TransactWriteItemsFacade
    {
        return new TransactWriteItemsFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function batchGetItem(): BatchGetItemFacade
    {
        return new BatchGetItemFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }

    public function batchWriteItem(): BatchWriteItemFacade
    {
        return new BatchWriteItemFacade(
            client: $this->client,
            marshaler: $this->marshaler,
            defaultTable: $this->singleTable,
        );
    }
}
