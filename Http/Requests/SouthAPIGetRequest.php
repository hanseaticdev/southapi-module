<?php

namespace Modules\SouthAPI\Http\Requests;

class SouthAPIGetRequest extends SouthAPIRequest
{
    protected static RequestMethodEnum $method = RequestMethodEnum::GET;

    const TRANSFERS_DATA_VIA_URL = true;
}
