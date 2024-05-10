<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Results\GetItemResult;

/**
 * @method GetItemResult dispatch()
 * @method PromiseInterface dispatchAsync()
 */
class GetItem extends \Terseq\Builders\GetItem
{
    use Essential;
}
