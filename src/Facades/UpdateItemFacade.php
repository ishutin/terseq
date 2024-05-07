<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\UpdateItem\UpdateItem;
use Terseq\Facades\Results\WriteOperationResult;

/**
 * @method WriteOperationResult dispatch(Closure|UpdateItem $builder)
 * @method PromiseInterface dispatchAsync(Closure|UpdateItem $builder)
 */
readonly class UpdateItemFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new UpdateItem(marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): WriteOperationResult
    {
        return WriteOperationResult::create(
            $this->client->updateItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        return $this->client
            ->updateItemAsync($builder->getQuery())
            ->then(fn (Result $result) => WriteOperationResult::create($result->toArray(), $this->marshaler));
    }
}
