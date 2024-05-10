<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Results\WriteOperationResult;

/**
 * @method WriteOperationResult dispatch()
 * @method PromiseInterface dispatchAsync()
 */
class DeleteItem extends \Terseq\Builders\DeleteItem
{
    use Essential;
}
