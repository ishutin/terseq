<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\Builder;

abstract readonly class Facade
{
    public function __construct(
        protected DynamoDbClient $client,
        protected Marshaler $marshaler = new Marshaler(),
    ) {
    }

    public function dispatch(Closure|Builder $builder): mixed
    {
        if (is_callable($builder)) {
            $builder = $builder($this->createBuilder());
        }

        return $this->performQuery($builder);
    }

    public function async(Closure|Builder $builder): PromiseInterface
    {
        if (is_callable($builder)) {
            $builder = $builder($this->createBuilder());
        }

        return $this->performQueryAsync($builder);
    }

    abstract protected function createBuilder(): Builder;

    abstract protected function performQuery(Builder $builder): mixed;

    abstract protected function performQueryAsync(Builder $builder): PromiseInterface;
}
