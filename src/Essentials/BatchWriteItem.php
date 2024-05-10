<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Results\BatchWriteItemResult;

/**
 * @method BatchWriteItemResult dispatch()
 * @method PromiseInterface dispatchAsync()
 */
class BatchWriteItem extends \Terseq\Builders\BatchWriteItem
{
    use Essential;
}
