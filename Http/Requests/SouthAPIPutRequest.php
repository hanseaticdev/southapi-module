<?php

namespace Modules\SouthAPI\Http\Requests;

class SouthAPIPutRequest extends SouthAPIRequest
{
    protected static RequestMethodEnum $method = RequestMethodEnum::PUT;

    const TRANSFERS_DATA_VIA_URL = false;
}
