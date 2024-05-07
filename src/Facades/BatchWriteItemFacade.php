<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\BatchWriteItem\BatchWriteItem;
use Terseq\Builders\Operations\Builder;
use Terseq\Facades\Results\BatchWriteItemResult;

/**
 * @method BatchWriteItemResult dispatch(Closure|BatchWriteItem $builder)
 * @method PromiseInterface dispatchAsync(Closure|BatchWriteItem $builder)
 */
readonly class BatchWriteItemFacade extends Facade
{
    protected function createBuilder(): BatchWriteItem
    {
        return new BatchWriteItem(marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): BatchWriteItemResult
    {
        return BatchWriteItemResult::create(
            $this->client->batchWriteItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        return $this->client->batchWriteItemAsync($builder->getQuery())
            ->then(fn ($result) => BatchWriteItemResult::create($result->toArray(), $this->marshaler));
    }
}
