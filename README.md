# Terseq: AWS DynamoDB Query Builder

## Capabilities

* Build a DynamoDB Query for next operations:
    * GetItem
    * PutItem
    * UpdateItem
    * DeleteItem
    * Query
    * TransactGetItems
    * TransactWriteItems
    * BatchGetItem
    * BatchWriteItem

* Call DynamoDB API with the query built

Package uses AWS SDK for PHP v3

## Installation

```bash
composer require aiotu/terseq
```

## Usage

### Create client by AWS SDK and factory

```php
$client = new \Aws\DynamoDb\DynamoDbClient([
    'region' => 'us-west-2',
    'version' => 'latest',
]);

$factory = new OperationFactory($client, new Marshaler());
```

### GetItem

```php
$query = \Terseq\Builders\Operations\GetItem\GetItem::build()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id');

$result = $factory->getItem()->dispatch($query);
```

### PutItem

```php
$query = \Terseq\Builders\Operations\PutItem\PutItem::build()
    ->table('Books')
    ->item([
        'BookId' => 'super-cool-id',
        'Title' => 'Super Cool Book',
        'Author' => 'Super Cool Author',
    ]);

$result = $factory->putItem()->dispatch($query);
```

### UpdateItem

```php
$query = \Terseq\Builders\Operations\UpdateItem\UpdateItem::build()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->set('Title', 'Super Cool Book Updated')
    ->set('Author', 'Super Cool Author Updated');

$result = $factory->updateItem()->dispatch($query);
```

### DeleteItem

```php
$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id');

$result = $factory->deleteItem()->dispatch($query);
```

### Query

```php
$query = \Terseq\Builders\Operations\Query\Query::build()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id')
    ->consistentRead();
   
$result = $factory->query()->dispatch($query); 
```

### TransactGetItems

```php
$query = \Terseq\Builders\Operations\TransactGetItems\TransactGetItems::build()
    ->get([
        fn (\Terseq\Builders\Operations\TransactGetItems\Operations\Get $get) => $get->pk('super-cool-id1'),
        fn (\Terseq\Builders\Operations\TransactGetItems\Operations\Get $get) => $get->pk('super-cool-id2'),
    ], table: ['Books', 'BookId']);

$result = $factory->transactGetItems()->dispatch($query);
```

### TransactWriteItems

```php
$query = \Terseq\Builders\Operations\TransactWriteItems\TransactWriteItems::build()
    ->put(
        [
            fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Put $put) => $put->item([
                'BookId' => 'super-book1',
                'Author' => 'Unknown',
            ]),
            fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Put $put) => $put->item([
                'BookId' => 'super-book-2',
                'Author' => 'Incognito',
            ]),
        ],
        table: ['Books', 'BookId']
    )
    ->update(
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Update $update) => $update->pk('super-book-3')
            ->set('Author', 'Incognito'),
        table: ['Books', 'BookId'],
    )
    ->delete(
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Delete $delete) => $delete->pk('super-book-4'),
        table: ['Books', 'BookId'],
    );

$result = $factory->transactWriteItems()->dispatch($query);
```

### BatchGetItem

```php

$query = \Terseq\Builders\Operations\BatchGetItem\BatchGetItem::build()
    ->get(,
        fn(\Terseq\Builders\Operations\BatchGetItem\Operations\BatchGet $get) => $get
            ->pk('super-book-1')
            ->pk('super-book-2'),
        table: ['Books', 'BookId'],
    );

$result = $factory->batchGetItem()->dispatch($query);
```

### BatchWriteItem

```php
$query = \Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem::build()
    ->put(
        [
            'BookId' => 'super-book-1',
            'Author' => 'Unknown',
        ],
        table: ['Books', 'BookId'],
);

$result = $factory->batchWriteItem()->dispatch($query);       
```

## Table name and keys

Table name and keys can be passed as array, string or object of `Terseq\Contracts\Builder\TableInterface`

### Example of passing as array

```php
$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(['TableName', 'PartitionKey', 'SortKey']);
```

OR

```php
$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(['TableName', 'PartitionKey']); // Sort key by default is null
```

OR

```php
$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(['TableName']); // Sort key by default is null, Partition key by default is 'Id'
```

OR

```php
$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table([
      'table' => 'TableName',
      'pk' => 'PartitionKey',
      'sk' => 'SortKey',
    ]);
```


### Example of passing as string

```php
$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table('TableName'); // Sort key by default is null, partition key by default is 'Id'
 ```

### Example of passing as object

```php
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

$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(new MyTable()); 
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

$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(new MyTable());
```

## Single-table design (recommended)

Library supports [single-table design](https://aws.amazon.com/blogs/compute/creating-a-single-table-design-with-amazon-dynamodb/).

### Example of using single-table design

```php
$factory = new OperationFactory(
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

#### Before

```php
// Query
$query = \Terseq\Builders\Operations\GetItem\GetItem::build()
    ->table(['Books', 'BookId'])
    ->pk('super-cool-id');

$factory->getItem()->dispatch($query);

// TransactWriteItems
$query = \Terseq\Builders\Operations\TransactWriteItems\TransactWriteItems::build()
    ->put(
        [
            fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Put $put) => $put->item([
                'BookId' => 'super-book1',
                'Author' => 'Unknown',
            ]),
            fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Put $put) => $put->item([
                'BookId' => 'super-book-2',
                'Author' => 'Incognito',
            ]),
        ],
        table: ['Books', 'BookId']
    )
    ->update(
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Update $update) => $update->pk('super-book-3')
            ->set('Author', 'Incognito'),
        table: ['Books', 'BookId'],
    )
    ->delete(
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Delete $delete) => $delete->pk('super-book-4'),
        table: ['Books', 'BookId'],
    );

$factory->transactWriteItems()->dispatch($query);
```

#### After

```php
// Query
$query = \Terseq\Builders\Operations\GetItem\GetItem::build()
    ->pk('super-cool-id');

$factory->getItem()->dispatch($query);

// TransactWriteItems
$query = \Terseq\Builders\Operations\TransactWriteItems\TransactWriteItems::build()
    ->put(
        [
            fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Put $put) => $put->item([
                'BookId' => 'super-book1',
                'Author' => 'Unknown',
            ]),
            fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Put $put) => $put->item([
                'BookId' => 'super-book-2',
                'Author' => 'Incognito',
            ]),
        ],
    )
    ->update(
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Update $update) => $update->pk('super-book-3')
            ->set('Author', 'Incognito'),
    )
    ->delete(
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Delete $delete) => $delete->pk('super-book-4'),
    );

$factory->transactWriteItems()->dispatch($query);
```