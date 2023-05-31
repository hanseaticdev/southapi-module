<?php

namespace Modules\SouthAPI\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Modules\HbSupport\TechnicalLogs\TechnicalLog;
use Modules\SouthAPI\Auth\ApiKeyAuth;
use Psr\Log\LogLevel;

/**
 * Class SouthApiException
 */
class SouthApiException extends Exception
{
    const INVALID_API_KEY = 'Invalid api key';

    const REQUEST_EXCEPTION = 'A technical error occurred.';

    protected string $logLevel;

    protected $detailedLog = null;

    protected ?TechnicalLog $technicalLog = null;

    /**
     * SouthApiException constructor.
     *
     * @param  string  $message optional message, if not given a default message is provided
     */
    public function __construct(string $message, string|array $reason)
    {
        parent::__construct($message);
        $this->logLevel = LogLevel::ERROR;
        $this->detailedLog = Arr::wrap($reason);
        $this->technicalLog = TechnicalLog::make();
    }

    public static function response401(): static
    {
        return new static(SouthApiException::INVALID_API_KEY, '401 response');
    }

    public static function codeInResponseBody(): static
    {
        return new static(SouthApiException::INVALID_API_KEY, 'Invalid code ('.ApiKeyAuth::INVALID_CREDENTIALS_BODY_CODE.') in body');
    }

    public static function emptyApiKey(): static
    {
        return new static(SouthApiException::INVALID_API_KEY, 'No api key set in .env file');
    }

    public static function requestException(array $data): static
    {
        return new static(SouthApiException::REQUEST_EXCEPTION, $data);
    }

    public function report()
    {
        $context = [
            'message' => $this->message,
        ];
        if ($this->detailedLog) {
            $context = array_merge($context, $this->detailedLog);
        }

        $this->technicalLog
            ->setLogLevel($this->logLevel)
            ->with($context)
            ->logException($this);
    }

    public function getDetailedErrorLog(): array
    {
        return $this->detailedLog;
    }
}
