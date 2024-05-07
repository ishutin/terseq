<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\BatchGetItem\BatchGetItem;
use Terseq\Builders\Operations\Builder;
use Terseq\Facades\Results\BatchGetItemResult;

/**
 * @method BatchGetItemResult dispatch(Closure|BatchGetItem $builder)
 * @method PromiseInterface dispatchAsync(Closure|BatchGetItem $builder)
 */
readonly class BatchGetItemFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new BatchGetItem(marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): BatchGetItemResult
    {
        return BatchGetItemResult::create(
            $this->client->batchGetItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        return $this->client->batchGetItemAsync($builder->getQuery())
            ->then(fn ($result) => BatchGetItemResult::create($result->toArray(), $this->marshaler));
    }
}
