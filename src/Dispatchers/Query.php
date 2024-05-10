<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Builders\Builder;
use Terseq\Builders\Casters\Caster;
use Terseq\Dispatchers\Results\QueryResult;

use function method_exists;

/**
 * @method QueryResult dispatch(Builders\Query $builder)
 * @method PromiseInterface dispatchAsync(Builders\Query $builder)
 */
readonly class Query extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): QueryResult
    {
        $result = $this->client->query($builder->getQuery());

        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return QueryResult::create($result->toArray(), $this->marshaler, $caster);
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return $this->client
            ->queryAsync($builder->getQuery())
            ->then(fn (Result $result) => QueryResult::create($result->toArray(), $this->marshaler, $caster));
    }
}
