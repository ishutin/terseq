<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\Result;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders;
use Terseq\Builders\Casters\Caster;
use Terseq\Dispatchers\Results\GetItemResult;

use function method_exists;

/**
 * @method GetItemResult dispatch(Closure|Builders\GetItem $builder)
 * @method PromiseInterface dispatchAsync(Closure|Builders\GetItem $builder)
 */
readonly class GetItem extends Dispatcher
{
    protected function performQuery(Builders\Builder $builder): GetItemResult
    {
        $result = $this->client->getItem($builder->getQuery());

        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return GetItemResult::create($result->toArray(), $this->marshaler, $caster);
    }

    protected function performQueryAsync(Builders\Builder $builder): PromiseInterface
    {
        $caster = (method_exists($builder, 'getCaster') ? $builder->getCaster() : null) ?? new Caster();

        return $this->client
            ->getItemAsync($builder->getQuery())
            ->then(fn (Result $result) => GetItemResult::create($result->toArray(), $this->marshaler, $caster));
    }
}
