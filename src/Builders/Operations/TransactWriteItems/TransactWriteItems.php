<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\TransactWriteItems;

use Closure;
use Terseq\Builders\Operations\Builder;
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

    public function conditionCheck(Closure $closure, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;
        $get = new ConditionCheck($table ? $clone->createOrGetTable($table) : null, $clone->marshaler);

        $clone->conditionCheck[] = $closure($get);

        return $clone;
    }

    public function put(Closure|array $closure, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;

        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $put = new Put(
                table: $table ? $clone->createOrGetTable($table) : null,
                marshaler: $clone->marshaler,
            );
            $clone->put[] = $callback($put);
        }

        return $clone;
    }

    public function delete(Closure|array $closure, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;
        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $delete = new Delete($table ? $clone->createOrGetTable($table) : null, $clone->marshaler);
            $clone->delete[] = $callback($delete);
        }

        return $clone;
    }

    public function update(Closure|array $closure, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;

        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $update = new Update($table ? $clone->createOrGetTable($table) : null, $clone->marshaler);
            $clone->update[] = $callback($update);
        }

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig();

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
