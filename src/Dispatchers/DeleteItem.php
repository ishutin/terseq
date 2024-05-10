<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Dispatchers\Results\WriteOperationResult;

/**
 * @method WriteOperationResult dispatch(Builders\DeleteItem $builder)
 * @method PromiseInterface dispatchAsync(Builders\DeleteItem $builder)
 */
readonly class DeleteItem extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): WriteOperationResult
    {
        return WriteOperationResult::create(
            $this->client->deleteItem($builder->getQuery())->toArray(),
            $this->marshaler,
        );
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        return $this->client
            ->deleteItemAsync($builder->getQuery())
            ->then(fn (Result $result) => WriteOperationResult::create($result->toArray(), $this->marshaler));
    }
}
