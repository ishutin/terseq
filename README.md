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
])

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
    ->get(['Books', 'BookId'], [
        fn (\Terseq\Builders\Operations\TransactGetItems\Operations\Get $get) => $get->pk('super-cool-id1'),
        fn (\Terseq\Builders\Operations\TransactGetItems\Operations\Get $get) => $get->pk('super-cool-id2'),
    ]);

$result = $factory->transactGetItems()->dispatch($query);
```

### TransactWriteItems

```php
$query = \Terseq\Builders\Operations\TransactWriteItems\TransactWriteItems::build()
    ->put(
        ['Books', 'BookId'],
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
        ['Books', 'BookId'],
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Update $update) => $update->pk('super-book-3')
            ->set('Author', 'Incognito'),
    )
    ->delete(
        ['Books', 'BookId'],
        fn (\Terseq\Builders\Operations\TransactWriteItems\Operations\Delete $delete) => $delete->pk('super-book-4'),
    );

$result = $factory->transactWriteItems()->dispatch($query);
```

### BatchGetItem

```php

$query = \Terseq\Builders\Operations\BatchGetItem\BatchGetItem::build()
    ->get(['Books', 'BookId'],
        fn(\Terseq\Builders\Operations\BatchGetItem\Operations\BatchGet $get) => $get
            ->pk('super-book-1')
            ->pk('super-book-2'),
    );

$result = $factory->batchGetItem()->dispatch($query);
```

### BatchWriteItem

```php
$query = \Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem::build()
    ->put(['Books', 'BookId'], [
            'BookId' => 'super-book-1',
            'Author' => 'Unknown',
        ]);

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
    ->table(['TableName', 'PartitionKey']); // Sort key by default is 'SK'
```

OR

```php
$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(['TableName']); // Sort key by default is 'SK', Partition key by default is 'PK'
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
    ->table('TableName'); // Sort key by default is 'SK', partition key by default is 'PK'
 ```

### Example of passing as object

```php
class MyTable extends \Terseq\Builders\Table 
{
    public function getTableName() : string{
        return 'MyTable';
    }
}

$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(new MyTable()); // Sort key by default is 'SK', partition key by default is 'PK'
```

OR

```php
class MyTable extends \Terseq\Builders\Table 
{
    public function getTableName() : string{
        return 'MyTable';
    }
    
    public function getPartitionKey() : string{
        return 'MyPartitionKey';
    }
    
    public function getSortKey() : string
    {
        return 'MySortKey';
    }
}

$query = \Terseq\Builders\Operations\DeleteItem\DeleteItem::build()
    ->table(new MyTable());
```