<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Dispatchers\Results\TransactGetItemsResult;

/**
 * @method TransactGetItemsResult dispatch(Builders\TransactGetItems $builder)
 * @method PromiseInterface dispatchAsync(Builders\TransactGetItems $builder)
 */
readonly class TransactGetItems extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): TransactGetItemsResult
    {
        return TransactGetItemsResult::create(
            $this->client->transactGetItems($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        return $this->client->transactGetItemsAsync($builder->getQuery())
            ->then(fn (Result $result) => TransactGetItemsResult::create($result->toArray(), $this->marshaler));
    }
}
