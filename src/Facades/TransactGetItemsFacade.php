<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\TransactGetItems\TransactGetItems;
use Terseq\Facades\Results\TransactGetItemsResult;

/**
 * @method TransactGetItemsResult dispatch(Closure|TransactGetItems $builder)
 * @method PromiseInterface dispatchAsync(Closure|TransactGetItems $builder)
 */
readonly class TransactGetItemsFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new TransactGetItems(marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): TransactGetItemsResult
    {
        return TransactGetItemsResult::create(
            $this->client->transactGetItems($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        return $this->client->transactGetItemsAsync($builder->getQuery())
            ->then(fn (Result $result) => TransactGetItemsResult::create($result->toArray(), $this->marshaler));
    }
}
