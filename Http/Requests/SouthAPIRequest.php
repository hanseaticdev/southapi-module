<?php

namespace Modules\SouthAPI\Http\Requests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Modules\HbSupport\TechnicalLogs\TechnicalLog;
use Modules\SouthAPI\Auth\ApiKeyAuth;
use Modules\SouthAPI\Auth\Auth;
use Modules\SouthAPI\Exceptions\SouthApiException;
use Modules\SouthAPI\Http\RequestOptions\JsonRequestOptions;
use Modules\SouthAPI\Http\Responses\SouthAPIResponse;
use Psr\Log\LogLevel;

abstract class SouthAPIRequest
{
    protected ClientInterface $backEndClient;

    protected string $southApiUrl;

    private Auth $auth;

    private string $latestPath;

    private array|object $latestData;

    private ?SouthAPIResponse $latestResponse;

    private float $startTime;

    private bool $checkIfResponseIsUnauthorized = true;

    protected static RequestMethodEnum $method = RequestMethodEnum::GET;

    const TRANSFERS_DATA_VIA_URL = true;

    public function __construct(
        ClientInterface $client,
        Auth $auth,
    ) {
        $this->backEndClient = $client;
        $this->auth = $auth;

        $this->southApiUrl = config('southapi.url');
    }

    /**
     * @throws SouthApiException
     */
    public function __invoke(string $southApiEndpointUrl, array $data): static
    {
        return $this->execute($southApiEndpointUrl, $data);
    }

    public function getBackEndClient(): ClientInterface
    {
        return $this->backEndClient;
    }

    public function getSouthApiUrl(string $uri = ''): string
    {
        return $this->southApiUrl.$uri;
    }

    protected function startClock(): self
    {
        $this->startTime = microtime(true);

        return $this;
    }

    protected function getRuntime(): float
    {
        return microtime(true) - $this->startTime;
    }

    /**
     * @throws SouthApiException
     */
    public function execute(string $endpointUri, array|object $data): static
    {
        $this->latestResponse = null;

        if (static::TRANSFERS_DATA_VIA_URL) {
            if (! empty($data)) {
                $endpointUri .= '?'.http_build_query($data);
            }
        }
        $this->latestPath = $endpointUri;
        $this->latestData = $data;

        $this->startClock();

        try {
            $response = $this->getBackEndClient()->request(
                static::$method->name,
                $this->getSouthApiUrl($endpointUri),
                $this->getOptions()
            );
        } catch (GuzzleException $exception) {
            throw SouthApiException::requestException([
                'exception' => $exception,
            ]);
        }

        $log = TechnicalLog::make();

        $this->latestResponse = new SouthAPIResponse(
            $response->getStatusCode(),
            (string) $response->getBody(),
            $this->getRuntime(),
            $log,
        );

        $this->logResponse($log);

        if ($this->checkIfResponseIsUnauthorized) {
            $this->throwErrorIfInvalidTokenResponse();
        }

        return $this;
    }

    protected function logResponse(TechnicalLog $technicalLog): static
    {
        $technicalLog
            ->setLogLevel($this->latestResponse->isUnsuccessful() ? LogLevel::ERROR : LogLevel::NOTICE)
            ->with([
                'url' => $this->latestPath,
                'payload' => (array) $this->latestData,
                'response' => $this->latestResponse->getJsonDecodedBody(),
                'response_raw' => ($this->latestResponse->getJsonDecodedBody() === null ? $this->latestResponse->getResultBody() : null),
                'statusCode' => $this->latestResponse->getStatusCode(),
                'runtime_seconds' => round($this->latestResponse->getRunTime(), 3),
            ])
            ->log($this->latestResponse->isUnsuccessful() ? 'SUEDLAYER_RESPONSE_ERROR' : 'SUEDLAYER_RESPONSE_SUCCESS');

        return $this;
    }

    public function getLatestResponse(): SouthAPIResponse
    {
        return $this->latestResponse;
    }

    protected function getOptions(): array
    {
        $options = new JsonRequestOptions();
        if (! static::TRANSFERS_DATA_VIA_URL) {
            $options->setBody($this->latestData);
        }
        $options = $this->auth->setAuthSpecificRequestOptions($options);

        return $options->getOptions();
    }

    /**
     * @throws SouthApiException
     */
    private function throwErrorIfInvalidTokenResponse(): void
    {
        if ($this->latestResponse->isUnauthorized()) {
            throw SouthApiException::response401();
        }

        if ($this->latestResponse->getFromBody('code') === ApiKeyAuth::INVALID_CREDENTIALS_BODY_CODE) {
            throw SouthApiException::codeInResponseBody();
        }
    }

    public function checkIfResponseIsUnauthorized(bool $check): static
    {
        $this->checkIfResponseIsUnauthorized = $check;

        return $this;
    }
}
