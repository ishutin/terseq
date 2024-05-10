<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Dispatchers\Results\TransactWriteItemsResult;

/**
 * @method TransactWriteItemsResult dispatch(Builders\TransactWriteItems $builder)
 * @method PromiseInterface dispatchAsync(Builders\TransactWriteItems $builder)
 */
readonly class TransactWriteItems extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): TransactWriteItemsResult
    {
        return TransactWriteItemsResult::create(
            $this->client->transactWriteItems($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        return $this->client->transactWriteItemsAsync($builder->getQuery())
            ->then(fn (Result $result) => TransactWriteItemsResult::create($result->toArray(), $this->marshaler));
    }
}
