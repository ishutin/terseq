<?php

declare(strict_types=1);

namespace Terseq\Builders\Exceptions;

use Exception;
use Terseq\Contracts\TerseqException;

class BuilderException extends Exception implements TerseqException
{
}
