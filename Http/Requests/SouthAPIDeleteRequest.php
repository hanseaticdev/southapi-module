<?php

namespace Modules\SouthAPI\Http\Requests;

class SouthAPIDeleteRequest extends SouthAPIRequest
{
    protected static RequestMethodEnum $method = RequestMethodEnum::DELETE;

    const TRANSFERS_DATA_VIA_URL = true;
}
