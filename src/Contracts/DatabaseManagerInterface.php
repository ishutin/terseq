<?php

declare(strict_types=1);

namespace Terseq\Contracts;

use Terseq\Essentials;
use Terseq\Essentials\DeleteItem;

interface DatabaseManagerInterface
{
    public function query(): Essentials\Query;

    public function getItem(): Essentials\GetItem;

    public function deleteItem(): DeleteItem;

    public function updateItem(): Essentials\UpdateItem;

    public function putItem(): Essentials\PutItem;

    public function transactGetItems(): Essentials\TransactGetItems;

    public function transactWriteItems(): Essentials\TransactWriteItems;

    public function batchGetItem(): Essentials\BatchGetItem;

    public function batchWriteItem(): Essentials\BatchWriteItem;
}
