<?php

declare(strict_types=1);

namespace Terseq\Contracts;

use Terseq\Facades\BatchGetItemFacade;
use Terseq\Facades\BatchWriteItemFacade;
use Terseq\Facades\DeleteItemFacade;
use Terseq\Facades\GetItemFacade;
use Terseq\Facades\PutItemFacade;
use Terseq\Facades\QueryFacade;
use Terseq\Facades\TransactGetItemsFacade;
use Terseq\Facades\TransactWriteItemsFacade;
use Terseq\Facades\UpdateItemFacade;

interface DatabaseManagerInterface
{
    public function query(): QueryFacade;

    public function getItem(): GetItemFacade;

    public function deleteItem(): DeleteItemFacade;

    public function updateItem(): UpdateItemFacade;

    public function putItem(): PutItemFacade;

    public function transactGetItems(): TransactGetItemsFacade;

    public function transactWriteItems(): TransactWriteItemsFacade;

    public function batchGetItem(): BatchGetItemFacade;

    public function batchWriteItem(): BatchWriteItemFacade;
}
