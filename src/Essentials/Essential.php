<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Dispatchers\Dispatcher;

trait Essential
{
    protected ?Dispatcher $dispatcher = null;

    public function setDispatcher(Dispatcher $dispatcher): static
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function dispatch(): mixed
    {
        return $this->dispatcher->dispatch($this);
    }

    public function dispatchAsync(): PromiseInterface
    {
        return $this->dispatcher->async($this);
    }
}
