<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Results\QueryResult;

/**
 * @method QueryResult dispatch()
 * @method PromiseInterface dispatchAsync()
 */
class Query extends \Terseq\Builders\Query
{
    use Essential;
}
