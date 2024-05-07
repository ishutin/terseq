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

    public function conditionCheck(TableInterface|string|array $table, Closure $closure): static
    {
        $clone = clone $this;
        $get = new ConditionCheck($clone->createTable($table), $clone->marshaler);

        $clone->conditionCheck[] = $closure($get);

        return $clone;
    }

    public function put(TableInterface|string|array $table, Closure|array $closure): static
    {
        $clone = clone $this;

        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $put = new Put($clone->createTable($table), $clone->marshaler);
            $clone->put[] = $callback($put);
        }

        return $clone;
    }

    public function delete(TableInterface|string|array $table, Closure|array $closure): static
    {
        $clone = clone $this;
        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $delete = new Delete($clone->createTable($table), $clone->marshaler);
            $clone->delete[] = $callback($delete);
        }

        return $clone;
    }

    public function update(TableInterface|string|array $table, Closure|array $closure): static
    {
        $clone = clone $this;

        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $update = new Update($clone->createTable($table), $clone->marshaler);
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

        $config['TransactItems'] = [];
        foreach ($this->put as $put) {
            $config['TransactItems'][] = ['Put' => $put->getQuery()];
        }

        foreach ($this->delete as $delete) {
            $config['TransactItems'][] = ['Delete' => $delete->getQuery()];
        }

        foreach ($this->update as $update) {
            $config['TransactItems'][] = ['Update' => $update->getQuery()];
        }

        foreach ($this->conditionCheck as $conditionCheck) {
            $config['TransactItems'][] = ['ConditionCheck' => $conditionCheck->getQuery()];
        }

        return $config;
    }
}
