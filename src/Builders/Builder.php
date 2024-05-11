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

abstract class Builder implements BuilderInterface
{
    use When;

    public ?ValuesStorage $valuesStorage = null;

    public ?TableInterface $table = null;

    /**
     * Allow table to be passed as string, array or TableInterface
     * If you pass array, it will be converted to TableInterface and use the array as table config: [table] or [table, pk] or [table, pk, sk] or ['table' => 'table', 'pk' => 'pk', 'sk' => 'sk']
     * @param TableInterface|array|null $table
     * @param Marshaler $marshaler
     * @throws BuilderException
     */
    public function __construct(
        TableInterface|array|null $table = null,
        public Marshaler $marshaler = new Marshaler(),
    ) {
        if ($table !== null) {
            $this->table = $this->createOrGetTable($table);
        }
    }


    public function table(TableInterface|array|null $table): static
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

    protected function createOrGetTable(TableInterface|array|null $table): TableInterface
    {
        if ($this->table) {
            return $this->table;
        }

        if ($table instanceof TableInterface) {
            return $table;
        }

        if (is_array($table)) {
            $tableName = $table[0] ?? $table['table'] ?? throw new BuilderException('Table name is required');
            $partitionKey = $table[1] ?? $table['pk'] ?? throw new BuilderException('Partition key is required');
            $sortKey = $table[2] ?? $table['sk'] ?? null;

            return new class ($tableName, $partitionKey, $sortKey) extends Table {
                public function __construct(
                    public readonly string $tableName,
                    public readonly string $partitionKey,
                    public readonly ?string $sortKey = null,
                ) {
                }

                public function getTableName(): string
                {
                    return $this->tableName;
                }

                public function getKeys(): Keys
                {
                    return new Keys(
                        partitionKey: $this->partitionKey,
                        sortKey: $this->sortKey,
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
