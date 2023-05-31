<?php

return [
    'url' => env('SOUTHAPI_URL'),
    'api_key' => env('SOUTHAPI_API_KEY'),
    'auth_interface' => \Modules\SouthAPI\Auth\ApiKeyAuth::class,
];
