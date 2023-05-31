<?php

namespace Modules\SouthAPI\Http\RequestOptions;

class JsonRequestOptions extends RequestOptions
{
    public function __construct()
    {
        $this->options = [
            \GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];
    }
}
