<?php

namespace Modules\SouthAPI;

use Modules\SouthAPI\Exceptions\SouthApiException;
use Modules\SouthAPI\Http\Requests\RequestMethodEnum;
use Modules\SouthAPI\Http\Responses\SouthAPIResponse;

/**
 * Interface ISouthAPI
 */
interface ISouthAPI
{
    /**
     * @throws SouthApiException
     */
    public function post(string $endpointUri, array|object $dataObject): SouthAPIResponse;

    /**
     * @throws SouthApiException
     */
    public function put(string $endpointUri, array|object $dataObject): SouthAPIResponse;

    /**
     * @throws SouthApiException
     */
    public function get(string $endpointUri, array $data): SouthAPIResponse;

    /**
     * @throws SouthApiException
     */
    public function delete(string $endpointUri, array $data): SouthAPIResponse;

    /**
     * @throws SouthApiException
     */
    public function request(RequestMethodEnum $method, string $endpointUri, array $data): SouthAPIResponse;
}
