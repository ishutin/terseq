# Terseq: AWS DynamoDB Query Builder

[![codecov](https://codecov.io/gh/ishutin/terseq/graph/badge.svg?token=SLX21EDHB1)](https://codecov.io/gh/ishutin/terseq)

This document provides a comprehensive guide on how to utilize the Terseq library to build and execute queries on AWS
DynamoDB using the AWS SDK for PHP.

## Features

### Terseq supports building queries for the following DynamoDB operations:

#### Single-item operations

- [GetItem](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_GetItem.html)
- [PutItem](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html)
- [UpdateItem](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_UpdateItem.html)
- [DeleteItem](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_DeleteItem.html)

#### Query operations

- [Query](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Query.html)

#### Transactions

- [TransactGetItems](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_TransactGetItems.html)
- [TransactWriteItems](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_TransactWriteItems.html)

#### Batch

- [BatchGetItem](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_BatchGetItem.html)
- [BatchWriteItem](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_BatchWriteItem.html)

## Why I should use this library?

AWS SDK for PHP is a powerful tool for working with AWS services, but it can be challenging to use due to its complexity. It requires a lot of boilerplate code to build and execute queries. Terseq simplifies this process by providing a fluent interface to build queries for DynamoDB operations. It also supports single-table design, which is a recommended practice for DynamoDB.

## Installation

To install the Terseq package, run the following command in your project directory using Composer:

```bash
composer require aiotu/terseq
```

## Usage

### Initialize

#### Create client by AWS SDK and DatabaseManager

```php
$client = new \Aws\DynamoDb\DynamoDbClient([
    'region' => 'us-west-2',
    'version' => 'latest',
]);

$manager = new \Terseq\DatabaseManager($client, new Marshaler());
```

### Operations

#### GetItem

```php
$manager->getItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->dispatch();
```

#### PutItem

```php
$manager->putItem()
    ->table(['Books', 'BookId'])
    ->item([
        'BookId' => 'super-cool-id',
        'Title' => 'Super Cool Book',
        'Author' => 'Super Cool Author',
    ])
    ->dispatch();
```

#### UpdateItem

```php
$manager->updateItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->set('Title', 'Super Cool Book Updated')
    ->set('Author', 'Super Cool Author Updated')
    ->dispatch();
```

#### DeleteItem

```php
$manager->deleteItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->dispatch();
```

#### Query

```php
$result = $manager->query()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->consistentRead()
    ->dispatch();
```

#### TransactGetItems

```php
use Terseq\Builders\Operations\TransactGetItems\Operations\Get;

$manager->transactGetItems()
    ->get(
            [
                static fn (Get $get) => $get->pk('super-cool-id1'),
                static fn (Get $get) => $get->pk('super-cool-id2'),
            ], 
            table: ['Books', 'BookId'],
        )
    ->dispatch();
```

#### TransactWriteItems

```php
use Terseq\Builders\Operations\TransactWriteItems\Operations\Delete;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Put;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Update;

$manager->transactWriteItems()
    ->put(
        [
            fn (Put $put) => $put->item([
                'BookId' => 'super-book1',
                'Author' => 'Unknown',
            ]),
            fn (Put $put) => $put->item([
                'BookId' => 'super-book-2',
                'Author' => 'Incognito',
            ]),
        ],
        table: ['Books', 'BookId'],
    )
    ->update(
        fn (Update $update) => $update
            ->pk('super-book-3')
            ->set('Author', 'Incognito'),
        table: ['Books', 'BookId'],
    )
    ->delete(
        fn (Delete $delete) => $delete->pk('super-book-4'),
        table: ['Books', 'BookId'],
    )
    ->dispatch();
```

#### BatchGetItem

```php
use Terseq\Builders\Operations\BatchGetItem\Operations\BatchGet;

$manager->batchGetItem()
    ->get(
        fn (BatchGet $get) => $get
            ->pk('super-book-1')
            ->pk('super-book-2')
            ->composite('book-id', 'release-date'),
        table: ['Books', 'BookId'],
    )
    ->dispatch();
```

#### BatchWriteItem

```php
$manager->batchWriteItem()
    ->put(
        [
            'BookId' => 'super-book-1',
            'Author' => 'Unknown',
        ],
        table: ['Books', 'BookId'],
    )
    ->put(
        [
            'BookId' => 'super-book-2',
            'Author' => 'Incognito',
        ],
        table: ['Books', 'BookId'],
    )
    ->delete(
        'super-book-3',
        table: ['Books', 'BookId'],
    )
    ->dispatch();
```

## Table

### Table as object (recommended)

#### Example of using table object

```php
use Terseq\Builders\Table;
use Terseq\Builders\Keys;

class Books extends Table 
{
    public function getTableName(): string
    {
        return 'Books';
    }

    public function getKeys(): Keys
    {
        return new Keys(partitionKey: 'BookId', sortKey: null);
    }
}
```

#### Example with [secondary indexes](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/HowItWorks.CoreComponents.html#HowItWorks.CoreComponents.SecondaryIndexes)

```php
use Terseq\Builders\Table;
use Terseq\Builders\Keys;

class BooksTable extends Table 
{
    /**
     * Table name
     */
    public function getTableName(): string
    {
        return 'Books';
    }

    /**
     * Partition key and sort key (optional)
     */
    public function getKeys(): Keys
    {
        return new Keys(partitionKey: 'BookId', sortKey: 'ReleaseDate');
    }

    /**
     * Secondary index map (optional) also known as GSI and LSI
     */
    public function getSecondaryIndexMap(): ?array
    {
        return [
            'AuthorIndex' => new Keys(partitionKey: 'AuthorId', sortKey: 'BornDate'),
            'GenreIndex' => new Keys(partitionKey: 'GenreId', sortKey: 'GenreName'),
            'LsiExample' => new Keys(partitionKey: 'BookId', sortKey: 'AuthorBornYear'),
        ];
    }
}
```

### Table as array

```php
table(table: ['TableName', 'PartitionKey', 'SortKey']);
```

OR

```php
table(table: ['TableName', 'PartitionKey']); // Sort key by default is null
```

OR

```php
table(table: ['TableName']); // throws exception, because PartitionKey is required
```

OR

```php
table(table: [
    'table' => 'TableName',
    'pk' => 'PartitionKey',
    'sk' => 'SortKey',
]);
```

## Single-table design (recommended)

Library
supports [single-table design](https://aws.amazon.com/blogs/compute/creating-a-single-table-design-with-amazon-dynamodb/).

### Example of using single-table design

```php
$manager = new \Terseq\DatabaseManager(
    client: $client, 
    marshaler: new Marshaler(),
    singleTable: new class extends \Terseq\Builders\Table {
        public function getTableName(): string
        {
            return 'Books';
        }
        
        public function getKeys(): Keys
        {
            return new Keys(partitionKey: 'BookId');
        }
    },
);
```

That's all! Now you can build queries without passing table name and keys.

#### Usage

```php
// Query
$manager->getItem()->pk('super-cool-id')->dispatch();

$manager->batchWriteItem()
    ->put(
        [
            'BookId' => 'super-book-1',
            'Author' => 'Unknown',
        ],
    )
    ->put(
        [
            'BookId' => 'super-book-2',
            'Author' => 'Incognito',
        ],
    )
    ->delete('super-book-3')
    ->dispatch();
```

## Comparison with AWS SDK

### Example of using AWS SDK

```php
$client->updateItem(
    [
        'TableName' => 'Books',
        'UpdateExpression' => 'SET #Title = :title_0, #Author = :author_0',
        'ExpressionAttributeNames' =>
            [
                '#Title' => 'Title',
                '#Author' => 'Author',
            ],
        'ExpressionAttributeValues' =>
            [
                ':title_0' =>
                    [
                        'S' => 'Super Cool Book Updated',
                    ],
                ':author_0' =>
                    [
                        'S' => 'Super Cool Author Updated',
                    ],
            ],
        'Key' =>
            [
                'BookId' =>
                    [
                        'S' => 'super-cool-id',
                    ],
            ],
    ],
);
```

### Example of using Terseq for the same operation

```php
$manager->updateItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->set('Title', 'Super Cool Book Updated')
    ->set('Author', 'Super Cool Author Updated')
    ->dispatch();
```
