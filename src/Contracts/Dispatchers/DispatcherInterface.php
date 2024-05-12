<?php

declare(strict_types=1);

namespace Terseq\Contracts\Dispatchers;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Builders\Builder;

interface DispatcherInterface
{
    public function dispatch(Builder $query): mixed;

    public function async(Builder $query): PromiseInterface;
}
