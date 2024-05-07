<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Casters\Caster;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\GetItem\GetItem;
use Terseq\Facades\Results\GetItemResult;

use function method_exists;

/**
 * @method GetItemResult dispatch(Closure|GetItem $builder)
 * @method PromiseInterface dispatchAsync(Closure|GetItem $builder)
 */
readonly class GetItemFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new GetItem(table: $this->defaultTable, marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): GetItemResult
    {
        $result = $this->client->getItem($builder->getQuery());

        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return GetItemResult::create($result->toArray(), $this->marshaler, $caster);
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return $this->client
            ->getItemAsync($builder->getQuery())
            ->then(fn (Result $result) => GetItemResult::create($result->toArray(), $this->marshaler, $caster));
    }
}
