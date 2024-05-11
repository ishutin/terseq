<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Closure;
use Terseq\Builders\Operations\TransactWriteItems\Operations\ConditionCheck;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Delete;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Put;
use Terseq\Builders\Operations\TransactWriteItems\Operations\Update;
use Terseq\Builders\Shared\BuilderParts\ClientRequestToken;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics;
use Terseq\Contracts\Builder\TableInterface;

class TransactWriteItems extends Builder
{
    use ReturnConsumedCapacity;
    use ReturnItemCollectionMetrics;
    use ClientRequestToken;


    /**
     * @var ConditionCheck[]
     */
    protected array $conditionCheck = [];

    /**
     * @var Put[]
     */
    protected array $put = [];

    /**
     * @var Delete[]
     */
    protected array $delete = [];

    /**
     * @var Update[]
     */
    protected array $update = [];

    public function conditionCheck(Closure|array $closure, TableInterface|array|null $table = null): static
    {
        $clone = clone $this;

        if ($closure instanceof Closure) {
            $closure = [$closure];
        }


        foreach ($closure as $callback) {
            $clone->conditionCheck[] = $callback(
                new ConditionCheck(
                    table: $table ? $clone->createOrGetTable($table) : null,
                    marshaler: $clone->marshaler,
                ),
            );
        }

        return $clone;
    }

    public function put(Closure|array $closure, TableInterface|array|null $table = null): static
    {
        $clone = clone $this;

        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $clone->put[] = $callback(
                new Put(
                    table: $table ? $clone->createOrGetTable($table) : null,
                    marshaler: $clone->marshaler,
                ),
            );
        }

        return $clone;
    }

    public function delete(Closure|array $closure, TableInterface|array|null $table = null): static
    {
        $clone = clone $this;
        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $clone->delete[] = $callback(
                new Delete($table ? $clone->createOrGetTable($table) : null, $clone->marshaler),
            );
        }

        return $clone;
    }

    public function update(Closure|array $closure, TableInterface|array|null $table = null): static
    {
        $clone = clone $this;

        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $clone->update[] = $callback(
                new Update($table ? $clone->createOrGetTable($table) : null, $clone->marshaler),
            );
        }

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig(withoutTable: true);

        $config = $this->appendReturnConsumedCapacity($config);
        $config = $this->appendReturnItemCollectionMetrics($config);
        $config = $this->appendClientRequestToken($config);


        foreach ($this->put as $put) {
            $config['TransactItems'][] = ['Put' => $put->table($this->table)->getQuery()];
        }

        foreach ($this->delete as $delete) {
            $config['TransactItems'][] = ['Delete' => $delete->table($this->table)->getQuery()];
        }

        foreach ($this->update as $update) {
            $config['TransactItems'][] = ['Update' => $update->table($this->table)->getQuery()];
        }

        foreach ($this->conditionCheck as $conditionCheck) {
            $config['TransactItems'][] = ['ConditionCheck' => $conditionCheck->table($this->table)->getQuery()];
        }

        return $config;
    }
}
