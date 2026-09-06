<?php
declare(strict_types=1);

use obray\ipp\Request;
use obray\ipp\exceptions\NetworkError;
use obray\ipp\test\CurlSpy;
use PHPUnit\Framework\TestCase;

/**
 * Exercise the real Request methods, replacing only cURL's network boundary.
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class RequestTransportTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/support/CurlSpy.php';
    }

    public function testTransportHasBoundedDefaultsAndSendsThePayload(): void
    {
        Request::sendRaw('ipps://printer.example/ipp/print', 'document bytes');

        $this->assertSame('https://printer.example:631/ipp/print', CurlSpy::$options[CURLOPT_URL]);
        $this->assertSame(10, CurlSpy::$options[CURLOPT_CONNECTTIMEOUT]);
        $this->assertSame(120, CurlSpy::$options[CURLOPT_TIMEOUT]);
        $this->assertSame('document bytes', CurlSpy::$options[CURLOPT_POSTFIELDS]);
        $this->assertContains('Content-Type: application/ipp', CurlSpy::$options[CURLOPT_HTTPHEADER]);
        $this->assertContains('Content-Length: 14', CurlSpy::$options[CURLOPT_HTTPHEADER]);
        $this->assertTrue(CurlSpy::$closed);
    }

    /** @dataProvider timeoutOverrides */
    public function testCallersCanOverrideTimeouts(array $options): void
    {
        Request::sendRaw('ipp://printer.example/', 'payload', null, null, $options);

        foreach ($options as $option) {
            $this->assertSame($option['value'], CurlSpy::$options[$option['key']]);
        }
        // Second and millisecond options control the same libcurl timer:
        // caller options must be applied after both defaults.
        $calls = array_column(CurlSpy::$calls, 0);
        $lastDefault = array_search(CURLOPT_TIMEOUT, $calls, true);
        foreach ($options as $option) {
            $positions = array_keys($calls, $option['key'], true);
            $this->assertGreaterThan($lastDefault, max($positions));
        }
    }

    public function timeoutOverrides(): array
    {
        return [
            'longer requests' => [[
                ['key' => CURLOPT_CONNECTTIMEOUT, 'value' => 30],
                ['key' => CURLOPT_TIMEOUT, 'value' => 600],
            ]],
            'unlimited total time for notifications' => [[
                ['key' => CURLOPT_TIMEOUT, 'value' => 0],
            ]],
            'millisecond timeouts' => [[
                ['key' => CURLOPT_CONNECTTIMEOUT_MS, 'value' => 250],
                ['key' => CURLOPT_TIMEOUT_MS, 'value' => 500],
            ]],
        ];
    }

    public function testTimeoutPreservesCurlErrorDetails(): void
    {
        CurlSpy::$errorCode = CURLE_OPERATION_TIMEDOUT;

        try {
            Request::sendRaw('ipp://printer.example/', 'payload');
            $this->fail('Expected a NetworkError.');
        } catch (NetworkError $exception) {
            $this->assertSame(CURLE_OPERATION_TIMEDOUT, $exception->getCurlErrorCode());
            $this->assertSame('ipp://printer.example/', $exception->getPrinterURI());
            $this->assertStringContainsString('Operation timed out', $exception->getMessage());
            $this->assertTrue(CurlSpy::$closed);
        }
    }

    public function testSuccessfulSubstitutionResponseIsReturnedByRealSendMethod(): void
    {
        // Independently encode an accepted job, including an ignored attribute.
        $attribute = static function (int $tag, string $name, string $value): string {
            return pack('Cn', $tag, strlen($name)) . $name . pack('n', strlen($value)) . $value;
        };
        CurlSpy::$body = pack('CCnN', 1, 1, 0x0001, 42)
            . "\x01" . $attribute(0x47, 'attributes-charset', 'utf-8')
            . $attribute(0x48, 'attributes-natural-language', 'en')
            . "\x05" . $attribute(0x44, 'media', 'unsupported-paper')
            . "\x02" . $attribute(0x21, 'job-id', pack('N', 123))
            . $attribute(0x45, 'job-uri', 'ipp://printer.example/jobs/123')
            . $attribute(0x23, 'job-state', pack('N', 3))
            . $attribute(0x44, 'job-state-reasons', 'none') . "\x03";

        $response = Request::send('ipp://printer.example/', 'payload');

        $this->assertSame(0x0001, $response->statusCode->getValue());
        $this->assertSame(123, $response->jobAttributes[0]->{'job-id'}->getAttributeValue());
        $this->assertSame('unsupported-paper', (string) $response->unsupportedAttributes[0]->media);
    }
}
