<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Casters\Caster;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\Query\Query;
use Terseq\Facades\Results\QueryResult;

use function method_exists;

/**
 * @method QueryResult dispatch(Closure|Query $builder)
 * @method PromiseInterface dispatchAsync(Closure|Query $builder)
 */
readonly class QueryFacade extends Facade
{
    protected function createBuilder(): Builder
    {
        return new Query(marshaler: $this->marshaler);
    }

    protected function performQuery(Builder $builder): QueryResult
    {
        $result = $this->client->query($builder->getQuery());

        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return QueryResult::create($result->toArray(), $this->marshaler, $caster);
    }

    protected function performQueryAsync(Builder $builder): PromiseInterface
    {
        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return $this->client
            ->queryAsync($builder->getQuery())
            ->then(fn (Result $result) => QueryResult::create($result->toArray(), $this->marshaler, $caster));
    }
}
