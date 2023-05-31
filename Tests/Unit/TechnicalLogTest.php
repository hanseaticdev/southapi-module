<?php

declare(strict_types=1);

namespace Modules\SouthAPI\Tests\Unit;

use Illuminate\Support\Facades\Log;
use Modules\HbSupport\TechnicalLogs\TechnicalLog;
use Modules\SouthAPI\Http\Responses\SouthAPIResponse;
use Psr\Log\LogLevel;
use Tests\TestCase;

/**
 * Class SouthAPITest
 *
 * @covers \App\Providers\HttpClientProvider
 * @covers \App\Exceptions\HttpResponseException
 */
class TechnicalLogTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_linking_with_response()
    {
        $logSpy = Log::spy();

        $response = new SouthAPIResponse(
            200,
            '{}',
            0.1,
            $responseLog = TechnicalLog::make(),
        );

        $log = TechnicalLog::make()
            ->setLogLevel(LogLevel::NOTICE)
            ->linkResponse($response)
            ->log('START');

        $logSpy->shouldHaveReceived('log')
            ->once()
            ->withArgs(function ($logLevel, $message, $context) use ($responseLog) {
                $this->assertEquals('notice', $logLevel);
                $this->assertEquals('START', $message);
                $this->assertTrue($this->assertIsSha1($context['log_id']));
                $this->assertEquals($responseLog->getId(), $context['response_log_id']);

                return true;
            });
    }
}
