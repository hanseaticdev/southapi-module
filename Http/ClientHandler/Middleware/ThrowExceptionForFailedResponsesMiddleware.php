<?php

namespace Modules\SouthAPI\Http\ClientHandler\Middleware;

use GuzzleHttp\Exception\GuzzleException;
use Modules\SouthAPI\Exceptions\SouthApiException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ThrowExceptionForFailedResponsesMiddleware extends AfterRequestMiddleware
{
    private function isResponseFailed($response): bool
    {
        return $response === null;
    }

    /**
     * @throws SouthApiException
     */
    public function afterRequest(RequestInterface $request, ?ResponseInterface $response, ?GuzzleException $reason)
    {
        if ($this->isResponseFailed($response)) {
            $data = [
                'message' => 'received null response from upstream',
                'uri' => $request->getUri()->getHost().' - '.$request->getUri()->getPath(),
                'method' => $request->getMethod(),
                'body' => (string) $request->getBody(),
            ];
            if ($reason) {
                $data['reason'] = $reason->getMessage();
            }

            throw SouthApiException::requestException($data);
        }
    }
}
