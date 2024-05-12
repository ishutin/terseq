<?php

declare(strict_types=1);

namespace Terseq\Tests\Fixtures;

use Aws\DynamoDb\DynamoDbClient;

class DynamoDbClientMock extends DynamoDbClient
{
    public function batchGetItem(array $args = [])
    {
    }

    public function batchGetItemAsync(array $args = [])
    {
    }

    public function batchWriteItem(array $args = [])
    {
    }

    public function batchWriteItemAsync(array $args = [])
    {
    }

    public function deleteItem(array $args = [])
    {
    }

    public function deleteItemAsync(array $args = [])
    {
    }

    public function getItem(array $args = [])
    {
    }

    public function getItemAsync(array $args = [])
    {
    }

    public function putItem(array $args = [])
    {
    }

    public function putItemAsync(array $args = [])
    {
    }

    public function query(array $args = [])
    {
    }

    public function queryAsync(array $args = [])
    {
    }

    public function updateItem(array $args = [])
    {
    }

    public function updateItemAsync(array $args = [])
    {
    }

    public function transactWriteItems(array $args = [])
    {
    }

    public function transactWriteItemsAsync(array $args = [])
    {
    }

    public function transactGetItems(array $args = [])
    {
    }

    public function transactGetItemsAsync(array $args = [])
    {
    }
}
