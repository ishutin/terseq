<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Dispatchers\Results\WriteOperationResult;

/**
 * @method WriteOperationResult dispatch(Builders\UpdateItem $builder)
 * @method PromiseInterface dispatchAsync(Builders\UpdateItem $builder)
 */
readonly class UpdateItem extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): WriteOperationResult
    {
        return WriteOperationResult::create(
            $this->client->updateItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        return $this->client
            ->updateItemAsync($builder->getQuery())
            ->then(fn (Result $result) => WriteOperationResult::create($result->toArray(), $this->marshaler));
    }
}
