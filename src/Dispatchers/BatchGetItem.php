<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Dispatchers\Results\BatchGetItemResult;

/**
 * @method BatchGetItemResult dispatch(Builders\BatchGetItem $builder)
 * @method PromiseInterface dispatchAsync(Builders\BatchGetItem $builder)
 */
readonly class BatchGetItem extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): BatchGetItemResult
    {
        return BatchGetItemResult::create(
            $this->client->batchGetItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        return $this->client->batchGetItemAsync($builder->getQuery())
            ->then(fn (Result $result) => BatchGetItemResult::create($result->toArray(), $this->marshaler));
    }
}
