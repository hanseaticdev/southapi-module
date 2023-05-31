<?php

namespace Modules\SouthAPI\Auth;

use Modules\SouthAPI\Http\RequestOptions\RequestOptions;

abstract class Auth
{
    abstract public function setAuthSpecificRequestOptions(RequestOptions $options): RequestOptions;
}
