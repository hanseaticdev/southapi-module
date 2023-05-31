<?php

namespace Modules\SouthAPI\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Modules\HbSupport\TechnicalLogs\TechnicalLog;

class SouthAPIResponse implements Arrayable
{
    const TOKEN_FAILED_HTTP_STATUS = 401;

    private int $statusCode;

    private string $resultBody;

    private float $runtime;

    public function __construct(
        int $statusCode,
        string $resultBody,
        float $runtime = 0,
        private ?TechnicalLog $technicalLog = null,
    ) {
        $this->statusCode = $statusCode;
        $this->resultBody = $resultBody;
        $this->runtime = $runtime;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResultBody(): string
    {
        return $this->resultBody;
    }

    public function getJsonDecodedBody(): array|null
    {
        return json_decode($this->getResultBody(), true);
    }

    /**
     * Gets value from array by key in dot notation
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getFromBody(string $key): mixed
    {
        return Arr::get($this->getJsonDecodedBody(), $key);
    }

    public function toArray(): array
    {
        return [
            'statusCode' => $this->getStatusCode(),
            'body' => $this->getJsonDecodedBody(),
        ];
    }

    public function getRuntime(): float
    {
        return $this->runtime;
    }

    public function isUnauthorized(): bool
    {
        return $this->getStatusCode() === static::TOKEN_FAILED_HTTP_STATUS;
    }

    public function isUnsuccessful(): bool
    {
        return $this->getStatusCode() >= 500 || $this->getStatusCode() === 401;
    }

    public function getTechnicalLog(): ?TechnicalLog
    {
        return $this->technicalLog;
    }

    public function getTechnicalLogId(): null|string
    {
        return $this->getTechnicalLog()?->getId();
    }
}
