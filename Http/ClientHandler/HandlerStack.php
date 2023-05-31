<?php

namespace Modules\SouthAPI\Http\ClientHandler;

use Modules\SouthAPI\Http\ClientHandler\Middleware\ThrowExceptionForFailedResponsesMiddleware;

class HandlerStack implements HandlerStackable
{
    /**
     * @var callable|null
     */
    private $handler;

    private \GuzzleHttp\HandlerStack $stack;

    public function __construct()
    {
        $this->handler = app()->has('abstractBackendClientHandler') ? app('abstractBackendClientHandler') : null;
        $this->stack = \GuzzleHttp\HandlerStack::create($this->handler);

        foreach ($this->getMiddleware() as $middleware) {
            $this->stack->push(app($middleware)->getMiddleware());
        }
    }

    private function getMiddleware(): array
    {
        return [
            ThrowExceptionForFailedResponsesMiddleware::class,
        ];
    }

    public function getStack(): \GuzzleHttp\HandlerStack
    {
        return $this->stack;
    }
}
