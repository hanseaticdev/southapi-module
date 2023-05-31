<?php

namespace Modules\SouthAPI;

use Modules\SouthAPI\Exceptions\SouthApiException;
use Modules\SouthAPI\Http\Requests\RequestMethodEnum;
use Modules\SouthAPI\Http\Requests\SouthAPIDeleteRequest;
use Modules\SouthAPI\Http\Requests\SouthAPIGetRequest;
use Modules\SouthAPI\Http\Requests\SouthAPIPostRequest;
use Modules\SouthAPI\Http\Requests\SouthAPIPutRequest;
use Modules\SouthAPI\Http\Responses\SouthAPIResponse;

/**
 * Class SouthAPI
 *
 * Base Class for Service Adapters to ISouthAPI Services
 */
class SouthAPI implements ISouthAPI
{
    private SouthAPIGetRequest $getRequest;

    private SouthAPIDeleteRequest $deleteRequest;

    private SouthAPIPostRequest $postRequest;

    private SouthAPIPutRequest $putRequest;

    public function __construct(
        SouthAPIGetRequest $getRequest,
        SouthAPIDeleteRequest $deleteRequest,
        SouthAPIPostRequest $postRequest,
        SouthAPIPutRequest $putRequest
    ) {
        $this->getRequest = $getRequest;
        $this->deleteRequest = $deleteRequest;
        $this->postRequest = $postRequest;
        $this->putRequest = $putRequest;
    }

    /**
     * @throws SouthApiException
     */
    public function get(string $endpointUri, array $data): SouthAPIResponse
    {
        return $this
            ->getRequest
            ->execute($endpointUri, $data)
            ->getLatestResponse();
    }

    /**
     * @throws SouthApiException
     */
    public function delete(string $endpointUri, array $data): SouthAPIResponse
    {
        return $this
            ->deleteRequest
            ->execute($endpointUri, $data)
            ->getLatestResponse();
    }

    /**
     * @throws SouthApiException
     */
    public function post(string $endpointUri, array|object $dataObject): SouthAPIResponse
    {
        return $this
            ->postRequest
            ->execute($endpointUri, $dataObject)
            ->getLatestResponse();
    }

    /**
     * @throws SouthApiException
     */
    public function put(string $endpointUri, array|object $dataObject): SouthAPIResponse
    {
        return $this
            ->putRequest
            ->execute($endpointUri, $dataObject)
            ->getLatestResponse();
    }

    /**
     * @throws SouthApiException
     */
    public function request(RequestMethodEnum $method, string $endpointUri, array|object $data): SouthAPIResponse
    {
        return match ($method) {
            RequestMethodEnum::GET => $this->get($endpointUri, $data),
            RequestMethodEnum::POST => $this->post($endpointUri, $data),
            RequestMethodEnum::PUT => $this->put($endpointUri, $data),
            RequestMethodEnum::DELETE => $this->delete($endpointUri, $data),
        };
    }
}
