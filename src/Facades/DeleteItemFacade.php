<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\DeleteItem\DeleteItem;
use Terseq\Facades\Results\WriteOperationResult;

/**
 * @method WriteOperationResult dispatch(Closure|DeleteItem $builder)
 * @method PromiseInterface dispatchAsync(Closure|DeleteItem $builder)
 */
readonly class DeleteItemFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new DeleteItem(table: $this->defaultTable, marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): WriteOperationResult
    {
        return WriteOperationResult::create(
            $this->client->deleteItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        return $this->client
            ->deleteItemAsync($builder->getQuery())
            ->then(fn (Result $result) => WriteOperationResult::create($result->toArray(), $this->marshaler));
    }
}
