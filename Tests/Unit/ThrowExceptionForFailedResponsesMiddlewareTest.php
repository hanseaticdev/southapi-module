<?php

declare(strict_types=1);

namespace Modules\SouthAPI\Tests\Unit;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Modules\SouthAPI\Exceptions\SouthApiException;
use Modules\SouthAPI\ISouthAPI;
use Tests\TestCase;

/**
 * Class AbstractSouthLayerServiceTest
 *
 * @covers \Modules\SouthAPI\Exceptions\SouthApiException
 */
class ThrowExceptionForFailedResponsesMiddlewareTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config([
            'southapi.url' => 'https://localhost',
            'southapi.api_key' => '12345',
        ]);

        $this->app->instance('abstractBackendClientHandler', new MockHandler());
    }

    /**
     * @return void
     */
    public function test_that_log_empty_responses_middleware_fires_exception_on_timed_out_or_broken_responses()
    {
        $this->addResponseExceptionMock(
            new RequestException('Timeout', new Request('POST', '/test-endpoint'))
        );

        try {
            $this
                ->getAbstractSouthLayerService()
                ->get('/test-endpoint', ['phpunit' => true]);
        } catch (SouthApiException $exception) {
            $this->assertEquals([
                'message' => 'received null response from upstream',
                'uri' => 'localhost - /test-endpoint',
                'method' => 'GET',
                'body' => '',
                'reason' => 'Timeout',
            ], $exception->getDetailedErrorLog());
        }
    }

    /**
     * @return void
     */
    public function test_that_log_empty_responses_middleware_does_not_fire_log_on_empty_responses()
    {
        $logSpy = Log::spy();

        $this
            ->addResponseMock(
                new Response(200, [], null)
            )
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);

        $logSpy->shouldNotHaveReceived('error');
    }

    /**
     * @return void
     */
    public function test_that_log_empty_responses_middleware_does_not_fire_log_on_filled_responses()
    {
        $logSpy = Log::spy();

        $this
            ->addResponseMock(
                new Response(200, [], '{}')
            )
            ->getAbstractSouthLayerService()
            ->get('/test-endpoint', ['phpunit' => true]);

        $logSpy->shouldNotHaveReceived('error');
    }

    private function addResponseMock(Response $response): ThrowExceptionForFailedResponsesMiddlewareTest
    {
        app('abstractBackendClientHandler')->append($response);

        return $this;
    }

    private function addResponseExceptionMock(GuzzleException $exception): ThrowExceptionForFailedResponsesMiddlewareTest
    {
        app('abstractBackendClientHandler')->append($exception);

        return $this;
    }

    private function getAbstractSouthLayerService(): ISouthAPI
    {
        return app(ISouthAPI::class);
    }

    protected function getResponseTimeoutException(): RequestException
    {
        return new RequestException('Timeout', new Request('POST', config('southapi.url')));
    }
}
