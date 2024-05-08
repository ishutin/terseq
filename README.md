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

#### Initialize

### Create client by AWS SDK and factory

```php
$client = new \Aws\DynamoDb\DynamoDbClient([
    'region' => 'us-west-2',
    'version' => 'latest',
]);

$factory = new OperationFactory($client, new Marshaler());
```

#### Use operations

### GetItem

```php
use Terseq\Builders\Operations\GetItem\GetItem;

$factory->getItem()->dispatch(
    static fn (GetItem $builder) => $builder
        ->table(['Books', 'BookId'])
        ->pk('super-cool-id'),
);
```

### PutItem

```php
use Terseq\Builders\Operations\PutItem\PutItem;

$factory->putItem()->dispatch(
    static fn (PutItem $builder) => $builder
        ->table(['Books', 'BookId'])
        ->item([
            'BookId' => 'super-cool-id',
            'Title' => 'Super Cool Book',
            'Author' => 'Super Cool Author',
        ]),
);
```

### UpdateItem

```php
use Terseq\Builders\Operations\UpdateItem\UpdateItem;

$factory->updateItem()->dispatch(
    static fn (UpdateItem $builder) => $builder
        ->table(['Books', 'BookId'])
        ->pk('super-cool-id')
        ->set('Title', 'Super Cool Book Updated')
        ->set('Author', 'Super Cool Author Updated'),
);
```

### DeleteItem

```php
use Terseq\Builders\Operations\DeleteItem\DeleteItem;

factory->deleteItem()->dispatch(
    static fn (DeleteItem $builder) => $builder
        ->table(['Books', 'BookId'])
        ->pk('super-cool-id'),
);
```

### Query

```php
use Terseq\Builders\Operations\Query\Query;
   
$result = $factory->query()->dispatch(
    static fn (Query $builder) => $builder
        ->table(['Books', 'BookId'])
        ->pk('super-cool-id')
        ->consistentRead(),
); 
```

### TransactGetItems

```php
use Terseq\Builders\Operations\TransactGetItems\TransactGetItems;
use \Terseq\Builders\Operations\TransactGetItems\Operations\Get;

$factory->transactGetItems()->dispatch(
    static fn (TransactGetItems $builder) => $builder
        ->get(
        [
            static fn (Get $get) => $get->pk('super-cool-id1'),
            static fn (Get $get) => $get->pk('super-cool-id2'),
        ], 
        table: ['Books', 'BookId']
    ),
);
```

### TransactWriteItems

```php
use Terseq\Builders\Operations\TransactWriteItems\TransactWriteItems;
use \Terseq\Builders\Operations\TransactWriteItems\Operations\Put;
use \Terseq\Builders\Operations\TransactWriteItems\Operations\Update;
use \Terseq\Builders\Operations\TransactWriteItems\Operations\Delete;

$factory->transactWriteItems()->dispatch(
    static fn (TransactWriteItems $builder) => $builder
        ->put(
            [
                static fn (Put $put) => $put->item([
                    'BookId' => 'super-book1',
                    'Author' => 'Unknown',
                ]),
                static fn (Put $put) => $put->item([
                    'BookId' => 'super-book-2',
                    'Author' => 'Incognito',
                ]),
            ],
            table: ['Books', 'BookId']
        )
        ->update(
            static fn (Update $update) => $update
                ->pk('super-book-3')
                ->set('Author', 'Incognito'),
            table: ['Books', 'BookId'],
        )
        ->delete(
            static fn (Delete $delete) => $delete
                ->pk('super-book-4'),
            table: ['Books', 'BookId'],
        ),
);
```

### BatchGetItem

```php
use Terseq\Builders\Operations\BatchGetItem\BatchGetItem;

$factory->batchGetItem()->dispatch(
    static fn (BatchGetItem $builder) => $builder
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
        ),
);
```

### BatchWriteItem

```php
use Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem;

$factory->batchWriteItem()->dispatch(
    static fn (BatchWriteItem $builder) => $builder
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
        ),
);       
```

## Table name and keys

Table name and keys can be passed as array, string or object of `Terseq\Contracts\Builder\TableInterface` (recommended)

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
use Terseq\Builders\Operations\BatchGetItem\BatchGetItem;

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

$factory->batchGetItem()->dispatch(
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
use Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem;
use Terseq\Builders\Operations\GetItem\GetItem;

// Query
$factory->getItem()->dispatch(
    static fn (GetItem $builder) => $builder
        ->table(['Books', 'BookId'])
        ->pk('super-cool-id'),
);

$factory->batchWriteItem()->dispatch(
    static fn (BatchWriteItem $builder) => $builder
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
        ),
);
```

#### After

```php
use Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem;
use Terseq\Builders\Operations\GetItem\GetItem;

// Query
$factory->getItem()->dispatch(
    static fn (GetItem $builder) => $builder->pk('super-cool-id'),
);

$factory->batchWriteItem()->dispatch(
    static fn (BatchWriteItem $builder) => $builder
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
        ),
);
```