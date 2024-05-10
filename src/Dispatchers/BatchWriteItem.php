<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Dispatchers\Results\BatchWriteItemResult;

/**
 * @method BatchWriteItemResult dispatch(Builders\BatchWriteItem $builder)
 * @method PromiseInterface dispatchAsync(Builders\BatchWriteItem $builder)
 */
readonly class BatchWriteItem extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): BatchWriteItemResult
    {
        return BatchWriteItemResult::create(
            $this->client->batchWriteItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        return $this->client->batchWriteItemAsync($builder->getQuery())
            ->then(fn ($result) => BatchWriteItemResult::create($result->toArray(), $this->marshaler));
    }
}
