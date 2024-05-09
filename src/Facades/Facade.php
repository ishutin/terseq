<?php

declare(strict_types=1);

namespace Terseq\Facades;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Operations\Builder;
use Terseq\Contracts\Builder\TableInterface;

use function is_callable;

abstract readonly class Facade
{
    public function __construct(
        protected DynamoDbClient $client,
        protected Marshaler $marshaler = new Marshaler(),
        protected ?TableInterface $defaultTable = null,
    ) {
    }

    public function dispatch(Closure|Builder $builder): mixed
    {
        return $this->performQuery(
            $this->makeBuilder($builder),
        );
    }

    public function async(Closure|Builder $builder): PromiseInterface
    {
        return $this->performQueryAsync(
            $this->makeBuilder($builder),
        );
    }

    public function getRawQuery(Closure|Builder $builder): array
    {
        return $this->makeBuilder($builder)->getQuery();
    }

    abstract protected function createBuilder(): Builder;

    abstract protected function performQuery(Builder $builder): mixed;

    abstract protected function performQueryAsync(Builder $builder): PromiseInterface;

    public function makeBuilder(Closure|Builder|null $builder = null): Builder
    {
        if ($builder === null) {
            $builder = $this->createBuilder();
        } elseif (is_callable($builder)) {
            $builderInstance = $this->createBuilder();

            if ($this->defaultTable) {
                $builderInstance = $builderInstance->table($this->defaultTable);
            }

            return $builder($builderInstance);
        }

        if ($this->defaultTable) {
            return $builder->table($this->defaultTable);
        }

        return $builder;
    }
}
