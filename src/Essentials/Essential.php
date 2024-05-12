<?php

declare(strict_types=1);

namespace Terseq\Essentials;

use GuzzleHttp\Promise\PromiseInterface;
use Terseq\Contracts\Dispatchers\DispatcherInterface;

trait Essential
{
    protected ?DispatcherInterface $dispatcher = null;

    public function setDispatcher(DispatcherInterface $dispatcher): static
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
