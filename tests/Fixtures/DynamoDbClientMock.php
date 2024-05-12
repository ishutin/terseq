<?php

declare(strict_types=1);

namespace Terseq\Tests\Fixtures;

use Aws\DynamoDb\DynamoDbClient;

class DynamoDbClientMock extends DynamoDbClient
{
    public function batchGetItem(array $args = [])
    {
    }

    public function batchGetItemAsync(array $args)
    {
    }

    public function batchWriteItem(array $args = [])
    {
    }

    public function batchWriteItemAsync(array $args)
    {
    }

    public function deleteItem(array $args = [])
    {
    }

    public function deleteItemAsync(array $args)
    {
    }

    public function getItem(array $args = [])
    {
    }

    public function getItemAsync(array $args)
    {
    }
}
