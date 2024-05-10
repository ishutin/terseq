# Terseq: AWS DynamoDB Query Builder

This document provides a comprehensive guide on how to utilize the Terseq library to build and execute queries on AWS
DynamoDB using the AWS SDK for PHP.

## Features

Terseq supports building queries for the following DynamoDB operations:

- GetItem
- PutItem
- UpdateItem
- DeleteItem
- Query
- TransactGetItems
- TransactWriteItems
- BatchGetItem
- BatchWriteItem

Package uses AWS SDK for PHP v3

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

### DatabaseManager usage

```php
use Terseq\Builders\GetItem;

$manager->getItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->dispatch();
```

### Operations

#### GetItem

```php
use Terseq\Builders\GetItem;

$manager->getItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->dispatch();
);
```

#### PutItem

```php
use Terseq\Builders\PutItem;

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
use Terseq\Builders\UpdateItem;

$manager->updateItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->set('Title', 'Super Cool Book Updated')
    ->set('Author', 'Super Cool Author Updated')
    ->dispatch();
```

#### DeleteItem

```php
use Terseq\Builders\DeleteItem;

$manager->deleteItem()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->dispatch();
```

#### Query

```php
use Terseq\Builders\Query;
   
$result = $manager->query()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->consistentRead()
    ->disaptch();
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
use Terseq\Builders\Operations\TransactWriteItems\Operations\Delete;use Terseq\Builders\Operations\TransactWriteItems\Operations\Put;use Terseq\Builders\Operations\TransactWriteItems\Operations\Update;

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


$manager->batchGetItem()
    ->get(
        [
            'BookId' => 'super-book-1',
            'Author' => 'Unknown',
        ],
        table: ['Books', 'BookId'],
    )
    ->get(
        [
            'BookId' => 'super-book-2',
            'Author' => 'Incognito',
        ],
        table: ['Books', 'BookId'],
    )
    ->dispatch();
```

#### BatchWriteItem

```php
use Terseq\Builders\BatchWriteItem;

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
        [
            'BookId' => 'super-book-3',
        ],
        table: ['Books', 'BookId'],
    )
    ->dispatch();
```

## Table name and keys

Table name and keys can be passed as array, string or object of `Terseq\Contracts\Builder\TableInterface` (recommended)

### Example of passing as array

```php
new DeleteItem(table: ['TableName', 'PartitionKey', 'SortKey']);
```

OR

```php
new DeleteItem(table: ['TableName', 'PartitionKey']); // Sort key by default is null
```

OR

```php
new DeleteItem(table: ['TableName']); // Sort key by default is null, Partition key by default is 'Id'
```

OR

```php
new DeleteItem(table: [
      'table' => 'TableName',
      'pk' => 'PartitionKey',
      'sk' => 'SortKey',
    ]);
```

### Example of passing as string

```php
new DeleteItem(table: 'TableName'); // Sort key by default is null, partition key by default is 'Id'
```

### Example of passing as object

```php
use Terseq\Builders\BatchGetItem;

class MyTable extends \Terseq\Builders\Table 
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

$table = new MyTable();

$manager->batchGetItem()->dispatch(
    static fn (BatchGetItem $builder) => $builder
        ->get(
            [
                'BookId' => 'super-book-1',
                'Author' => 'Unknown',
            ],
            table: $table,
        )
        ->get(
            [
                'BookId' => 'super-book-2',
                'Author' => 'Incognito',
            ],
            table: $table,
        ),
);
```

OR

```php
class MyTable extends \Terseq\Builders\Table 
{
    public function getTableName(): string
    {
        return 'Books';
    }

    public function getKeys(): Keys
    {
        return new Keys(partitionKey: 'BookId', sortKey: 'Year');
    }

    public function getGlobalSecondaryIndexMap(): ?array
    {
        return [
            'AuthorIndex' => new Keys(partitionKey: 'AuthorId', sortKey: 'AuthorBornYear'),
        ];
    }
}
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
$manager->getItem()->pk('super-cool-id')->disaptch();

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
    ->delete(
        [
            'BookId' => 'super-book-3',
        ],
    )
    ->dispatch();
```