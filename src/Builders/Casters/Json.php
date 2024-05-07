<?php

declare(strict_types=1);

namespace Terseq\Builders\Casters;

use stdClass;
use Terseq\Contracts\Facades\Casters\CasterInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

readonly class Json implements CasterInterface
{
    public function __construct(
        protected ?bool $assoc = null,
        protected int $depth = 512,
        protected int $flags = JSON_THROW_ON_ERROR,
    ) {
    }

    public function cast(mixed $value): array|stdClass
    {
        return json_decode($value, $this->assoc, $this->depth, $this->flags);
    }
}
