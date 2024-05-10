<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Results\TransactGetItemsResult;

/**
 * @method TransactGetItemsResult dispatch()
 * @method PromiseInterface dispatchAsync()
 */
class TransactGetItems extends \Terseq\Builders\TransactGetItems
{
    use Essential;
}
