<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\TransactWriteItems\TransactWriteItems;
use Terseq\Facades\Results\TransactWriteItemsResult;

/**
 * @method TransactWriteItemsResult dispatch(Closure|TransactWriteItems $builder)
 * @method PromiseInterface dispatchAsync(Closure|TransactWriteItems $builder)
 */
readonly class TransactWriteItemsFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new TransactWriteItems(table: $this->defaultTable, marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): TransactWriteItemsResult
    {
        return TransactWriteItemsResult::create(
            $this->client->transactWriteItems($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        return $this->client->transactWriteItemsAsync($builder->getQuery())
            ->then(fn (Result $result) => TransactWriteItemsResult::create($result->toArray(), $this->marshaler));
    }
}
