<?php

declare(strict_types=1);

namespace Modules\SouthAPI\Tests\Unit;

use Modules\SouthAPI\Auth\ApiKeyAuth;
use Modules\SouthAPI\Exceptions\SouthApiException;
use Modules\SouthAPI\Http\RequestOptions\JsonRequestOptions;
use Tests\TestCase;

/**
 * Class SouthAPITest
 *
 * @covers \App\Providers\HttpClientProvider
 * @covers \App\Providers\CustomerDataProvider
 * @covers \App\Exceptions\HttpResponseException
 */
class ApiKeyAuthTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config([
            'southapi.api_key' => '1234',
        ]);
    }

    public function test_that_key_gets_set_correctly()
    {
        $options = new JsonRequestOptions();
        $result = app(ApiKeyAuth::class)->setAuthSpecificRequestOptions($options);

        $this->assertEquals('1234', $result->getOptions()['headers']['apikey']);
    }

    public function test_that_error_gets_thrown_if_key_is_empty()
    {
        config([
            'southapi.api_key' => null,
        ]);

        $this->expectException(SouthApiException::class);

        $options = new JsonRequestOptions();
        app(ApiKeyAuth::class)->setAuthSpecificRequestOptions($options);
    }
}
