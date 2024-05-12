<?php

declare(strict_types=1);

namespace Terseq\Tests\Helpers;

use Aws\Result;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

trait DispatcherTestHelper
{
    protected function createResult(string $json): Result
    {
        return new Result(
            json_decode($json, true, 512, JSON_THROW_ON_ERROR),
        );
    }

    protected function createPromise(Result $result): PromiseInterface
    {
        $promise = new Promise();
        $promise->resolve($result);

        return $promise;
    }
}
