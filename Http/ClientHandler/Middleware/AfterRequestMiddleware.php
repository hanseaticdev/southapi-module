<?php

namespace Modules\SouthAPI\Http\ClientHandler\Middleware;

use Closure;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Create;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AfterRequestMiddleware extends Middleware
{
    public function getMiddleware(): Closure
    {
        return function (callable $handler) {
            return function ($request, array $options) use ($handler) {
                return $handler($request, $options)->then(
                    function ($response) use ($request) {
                        $this->afterRequest($request, $response, null);

                        return $response;
                    },
                    function ($reason) use ($request) {
                        $this->afterRequest($request, null, $reason);

                        return Create::rejectionFor($reason);
                    }
                );
            };
        };
    }

    abstract public function afterRequest(RequestInterface $request, ?ResponseInterface $response, ?GuzzleException $reason);
}
