<?php

namespace Modules\SouthAPI\Auth;

use Modules\SouthAPI\Exceptions\SouthApiException;
use Modules\SouthAPI\Http\RequestOptions\RequestOptions;

class ApiKeyAuth extends Auth
{
    public const INVALID_CREDENTIALS_BODY_CODE = '900901';

    /**
     * @throws SouthApiException
     */
    public function setAuthSpecificRequestOptions(RequestOptions $options): RequestOptions
    {
        if (! config('southapi.api_key')) {
            throw SouthApiException::emptyApiKey();
        }
        $options->setHeader('apikey', config('southapi.api_key'));

        return $options;
    }
}
