<?php

declare(strict_types=1);

namespace Modules\SouthAPI\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Modules\SouthAPI\Auth\ApiKeyAuth;
use Modules\SouthAPI\Exceptions\SouthApiException;
use Modules\SouthAPI\Http\Responses\SouthAPIResponse;
use Modules\SouthAPI\ISouthAPI;
use Psr\Log\LogLevel;
use Tests\TestCase;

/**
 * Class SouthAPITest
 *
 * @covers \Modules\SouthAPI\SouthAPI
 * @covers \Modules\SouthAPI\Http\Responses\SouthAPIResponse
 * @covers \Modules\SouthAPI\Exceptions\SouthApiException
 */
class SouthAPITest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config([
            'southapi.url' => 'https://localhost',
            'southapi.api_key' => '12345',
        ]);

        app()->instance(ClientInterface::class,
            $this->getMockBuilder(Client::class)
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->disableArgumentCloning()
                ->disallowMockingUnknownTypes()
                ->getMock()
        );
    }

    /**
     * @throws SouthApiException
     */
    public function testGetFromCache()
    {
        $response = new Response(201, [], '');
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $result = $this
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);
        $this->assertInstanceOf(SouthAPIResponse::class, $result);
    }

    /**
     * @throws SouthApiException
     */
    public function testPostFromCache()
    {
        $response = new Response(201, [], '');
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $result = $this
            ->getAbstractSouthLayerService()
            ->post('/test-endpoint', ['phpunit' => true]);
        $this->assertInstanceOf(SouthAPIResponse::class, $result);
    }

    /**
     * @throws SouthApiException
     */
    public function testPutFromCache()
    {
        $response = new Response(201, [], '');
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $result = $this
            ->getAbstractSouthLayerService()
            ->put('/test-endpoint', ['phpunit' => true]);
        $this->assertInstanceOf(SouthAPIResponse::class, $result);
    }

    /**
     * @throws SouthApiException
     */
    public function testDeleteFromCache()
    {
        $response = new Response(201, [], '');
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $result = $this
            ->getAbstractSouthLayerService()
            ->delete('/test-endpoint', ['phpunit' => true]);
        $this->assertInstanceOf(SouthAPIResponse::class, $result);
    }

    /**
     * @throws SouthApiException
     */
    public function testErrorWhenApiKeyIsEmpty()
    {
        config([
            'southapi.api_key' => null,
        ]);

        $this->expectException(SouthApiException::class);

        $result = $this
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);
        $this->assertInstanceOf(SouthAPIResponse::class, $result);
    }

    /**
     * @throws SouthApiException
     */
    public function testErrorIfUnauthorized()
    {
        $this->expectException(SouthApiException::class);

        $response = new Response(401, [], '');
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $result = $this
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);
        $this->assertInstanceOf(SouthAPIResponse::class, $result);
    }

    /**
     * @throws SouthApiException
     */
    public function testErrorIfApiKeyIsInvalid()
    {
        $this->expectException(SouthApiException::class);

        $response = new Response(200, [], json_encode([
            'code' => ApiKeyAuth::INVALID_CREDENTIALS_BODY_CODE,
        ]));
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $result = $this
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);
        $this->assertInstanceOf(SouthAPIResponse::class, $result);
    }

    /**
     * @throws SouthApiException
     */
    public function testLoggingOnSuccessfulResponse()
    {
        $logSpy = Log::spy();

        $response = new Response(201, [], '');
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $this
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);

        $logSpy->shouldHaveReceived('log')
            ->once()
            ->withArgs(function ($level, $message, $payload) {
                $this->assertEquals(LogLevel::NOTICE, $level);
                $this->assertEquals('SUEDLAYER_RESPONSE_SUCCESS', $message);

                return true;
            });
    }

    /**
     * @throws SouthApiException
     */
    public function testLoggingOnNotSuccessfulResponse()
    {
        $logSpy = Log::spy();

        $response = new Response(500, [], '{}');
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);
        $this
            ->getAbstractSouthLayerService()
            ->post('/test-endpoint', $body = ['phpunit' => true]);

        $logSpy->shouldHaveReceived('log')
            ->once()
            ->withArgs(function ($level, $message, $payload) use ($body) {
                $this->assertEquals(LogLevel::ERROR, $level);
                $this->assertEquals('SUEDLAYER_RESPONSE_ERROR', $message);

                unset($payload['runtime_seconds']);
                unset($payload['log_id']);
                $this->assertEquals([
                    'payload' => $body,
                    'url' => '/test-endpoint',
                    'response' => [],
                    'response_raw' => null,
                    'statusCode' => 500,
                ], $payload);

                return true;
            });
    }

    public function testLoggingIsMasked()
    {
        $logSpy = Log::spy();

        $response = new Response(201, [], json_encode([
            'email' => 'max-michael@mustermann.de',
        ]));
        $this->mock(ClientInterface::class)->expects('request')->andReturn($response);

        $this
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);

        $logSpy->shouldHaveReceived('log')
            ->once()
            ->withArgs(function ($level, $message, $payload) {
                $this->assertEquals(LogLevel::NOTICE, $level);
                $this->assertEquals('SUEDLAYER_RESPONSE_SUCCESS', $message);
                $this->assertEquals('max∗∗∗∗∗∗∗∗@mustermann.de', $payload['response']['email']);

                return true;
            });
    }

    private function getAbstractSouthLayerService(): ISouthAPI
    {
        return app(ISouthAPI::class);
    }
}
