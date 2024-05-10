<?php

declare(strict_types=1);

namespace Terseq\Dispatchers;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Builder;

abstract readonly class Dispatcher
{
    public function __construct(
        protected DynamoDbClient $client,
        protected Marshaler $marshaler = new Marshaler(),
    ) {
    }

    public function dispatch(Builder $query): mixed
    {
        return $this->performQuery($query);
    }

    public function async(Builder $query): PromiseInterface
    {
        return $this->performQueryAsync($query);
    }

    abstract protected function performQuery(Builder $builder): mixed;

    abstract protected function performQueryAsync(Builder $builder): PromiseInterface;
}
