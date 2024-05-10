<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Aws\DynamoDb\Marshaler;
use Terseq\Builders\Exceptions\BuilderException;
use Terseq\Builders\Shared\Extends\When;
use Terseq\Builders\Shared\ValuesStorage;
use Terseq\Contracts\Builder\BuilderInterface;
use Terseq\Contracts\Builder\TableInterface;

use function is_array;
use function is_string;

abstract class Builder implements BuilderInterface
{
    use When;

    public ?ValuesStorage $valuesStorage = null;

    public ?TableInterface $table = null;

    /**
     * Allow table to be passed as string, array or TableInterface
     * If you pass string, it will be converted to TableInterface and use the string as table name
     * If you pass array, it will be converted to TableInterface and use the array as table config: [table] or [table, pk] or [table, pk, sk] or ['table' => 'table', 'pk' => 'pk', 'sk' => 'sk']
     * @param TableInterface|string|array|null $table
     * @param Marshaler $marshaler
     */
    public function __construct(
        TableInterface|string|array|null $table = null,
        public Marshaler $marshaler = new Marshaler(),
    ) {
        if ($table !== null) {
            $this->table = $this->createOrGetTable($table);
        }
    }


    public function table(TableInterface|string|array|null $table): static
    {
        if ($this->table !== null) {
            return $this;
        }

        if ($table === null) {
            return $this;
        }

        $clone = clone $this;
        $clone->table = $clone->createOrGetTable($table);

        return $clone;
    }

    protected function createConfig(bool $withoutTable = false): array
    {
        if ($this->table === null || $withoutTable) {
            return [];
        }

        return [
            'TableName' => $this->table->getTableName(),
        ];
    }

    protected function createOrGetTable(TableInterface|array|string|null $table): TableInterface
    {
        if ($this->table) {
            return $this->table;
        }

        if ($table instanceof TableInterface) {
            return $table;
        }

        if (is_string($table)) {
            return new class ($table) extends Table {
                public function __construct(public string $tableName)
                {
                }

                public function getTableName(): string
                {
                    return $this->tableName;
                }

                public function getKeys(): Keys
                {
                    return new Keys(
                        partitionKey: 'Id',
                    );
                }
            };
        }

        if (is_array($table)) {
            return new class ($table) extends Table {
                public function __construct(public array $table)
                {
                }

                public function getTableName(): string
                {
                    return $this->table[0] ?? $this->table['table'] ?? throw new BuilderException(
                        'Table name is required',
                    );
                }

                public function getKeys(): Keys
                {
                    return new Keys(
                        partitionKey: $this->table[1] ?? $this->table['pk'] ?? 'Id',
                        sortKey: $this->table[2] ?? $this->table['sk'] ?? null,
                    );
                }
            };
        }

        throw new BuilderException('Table is required');
    }

    public function getValuesStorage(): ValuesStorage
    {
        if ($this->valuesStorage === null) {
            $this->valuesStorage = new ValuesStorage();
        }

        return $this->valuesStorage;
    }
}
