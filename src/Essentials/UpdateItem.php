<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Results\TransactWriteItemsResult;

/**
 * @method TransactWriteItemsResult dispatch()
 * @method PromiseInterface dispatchAsync()
 */
class UpdateItem extends \Terseq\Builders\UpdateItem
{
    use Essential;
}
