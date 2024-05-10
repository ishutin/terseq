<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Results\BatchGetItemResult;

/**
 * @method BatchGetItemResult dispatch()
 * @method PromiseInterface dispatchAsync()
 */
class BatchGetItem extends \Terseq\Builders\BatchGetItem
{
    use Essential;
}
