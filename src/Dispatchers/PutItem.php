<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Dispatchers\Results\WriteOperationResult;

/**
 * @method WriteOperationResult dispatch(Builders\PutItem $builder)
 * @method PromiseInterface dispatchAsync(Builders\PutItem $builder)
 */
readonly class PutItem extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): WriteOperationResult
    {
        return WriteOperationResult::create(
            $this->client->putItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        return $this->client
            ->putItemAsync($builder->getQuery())
            ->then(fn (Result $result) => WriteOperationResult::create($result->toArray(), $this->marshaler));
    }
}
