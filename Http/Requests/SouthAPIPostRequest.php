<?php

namespace Modules\SouthAPI\Http\Requests;

class SouthAPIPostRequest extends SouthAPIRequest
{
    protected static RequestMethodEnum $method = RequestMethodEnum::POST;

    const TRANSFERS_DATA_VIA_URL = false;
}
