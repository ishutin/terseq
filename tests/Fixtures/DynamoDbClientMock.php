<?php

declare(strict_types=1);

namespace Terseq\Tests\Fixtures;

use Aws\DynamoDb\DynamoDbClient;

class DynamoDbClientMock extends DynamoDbClient
{
    public function batchGetItem(array $args = [])
    {
    }
}
