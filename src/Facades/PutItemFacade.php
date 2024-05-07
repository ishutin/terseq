<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\PutItem\PutItem;
use Terseq\Facades\Results\WriteOperationResult;

/**
 * @method WriteOperationResult dispatch(Closure|PutItem $builder)
 * @method PromiseInterface dispatchAsync(Closure|PutItem $builder)
 */
readonly class PutItemFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new PutItem(marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): WriteOperationResult
    {
        return WriteOperationResult::create(
            $this->client->putItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        return $this->client
            ->putItemAsync($builder->getQuery())
            ->then(fn (Result $result) => WriteOperationResult::create($result->toArray(), $this->marshaler));
    }
}
