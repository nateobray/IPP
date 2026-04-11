<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use obray\ipp\exceptions\IppStatusException;

/**
 * A minimal request stub that decodes a pre-loaded response binary and
 * applies the same status-check logic as Request::send(), so we can verify
 * the IppStatusException flow without a real network connection.
 */
class ErrorReplayRequest implements \obray\ipp\interfaces\RequestInterface
{
    private static string $responseBinary = '';

    public static function setResponseBinary(string $binary): void
    {
        self::$responseBinary = $binary;
    }

    public static function send(
        string $printerURI,
        string $encodedPayload,
        ?string $user = null,
        ?string $password = null,
        array $curlOptions = []
    ): \obray\ipp\transport\IPPPayload {
        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode(self::$responseBinary);

        if ($payload->statusCode->getValue() >= 0x0100) {
            throw new IppStatusException($printerURI, $payload);
        }

        return $payload;
    }
}

final class IppStatusExceptionTest extends TestCase
{
    // --- Unit tests for the exception class itself ---

    public function testExceptionCarriesPrinterUriStatusAndResponse(): void
    {
        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->statusCode = new \obray\ipp\types\StatusCode(
            \obray\ipp\types\StatusCode::client_error_document_format_not_supported
        );

        $printerURI = 'ipp://localhost/printers/PDF';
        $e = new IppStatusException($printerURI, $payload);

        $this->assertSame($printerURI, $e->getPrinterURI());
        $this->assertSame($payload, $e->getResponse());
        $this->assertSame($payload->statusCode, $e->getStatusCode());
        $this->assertStringContainsString($printerURI, $e->getMessage());
        $this->assertStringContainsString('client-error-document-format-not-supported', $e->getMessage());
        $this->assertSame(
            \obray\ipp\types\StatusCode::client_error_document_format_not_supported,
            $e->getCode()
        );
    }

    public function testExceptionIsRuntimeException(): void
    {
        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->statusCode = new \obray\ipp\types\StatusCode(
            \obray\ipp\types\StatusCode::server_error_internal_error
        );
        $e = new IppStatusException('ipp://localhost/', $payload);

        $this->assertInstanceOf(\RuntimeException::class, $e);
    }

    // --- Fixture-backed integration tests ---

    /** @dataProvider errorFixtureProvider */
    public function testPrinterThrowsIppStatusExceptionForErrorResponse(string $metaPath): void
    {
        $meta = json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
        $responseBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['response_file']);

        ErrorReplayRequest::setResponseBinary($responseBinary);

        $printer = new \obray\ipp\Printer(
            $meta['printer_uri'],
            '',
            '',
            [],
            ErrorReplayRequest::class
        );

        $this->expectException(IppStatusException::class);

        // Any method will do — validateJob is cheap (no document data)
        $printer->validateJob();
    }

    /** @dataProvider errorFixtureProvider */
    public function testThrownExceptionCarriesCorrectStatusCode(string $metaPath): void
    {
        $meta = json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
        $responseBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['response_file']);

        ErrorReplayRequest::setResponseBinary($responseBinary);

        $printer = new \obray\ipp\Printer(
            $meta['printer_uri'],
            '',
            '',
            [],
            ErrorReplayRequest::class
        );

        try {
            $printer->validateJob();
            $this->fail('Expected IppStatusException was not thrown.');
        } catch (IppStatusException $e) {
            $expectedStatus = $meta['summary']['status'];
            $this->assertSame($expectedStatus, (string) $e->getStatusCode());
            $this->assertSame($meta['printer_uri'], $e->getPrinterURI());
            $this->assertNotNull($e->getResponse());
            $this->assertSame('client-error', $e->getStatusCode()->getClass());
        }
    }

    /** @dataProvider errorFixtureProvider */
    public function testResponsePayloadIsAccessibleOnException(string $metaPath): void
    {
        $meta = json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
        $responseBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['response_file']);

        ErrorReplayRequest::setResponseBinary($responseBinary);

        $printer = new \obray\ipp\Printer(
            $meta['printer_uri'],
            '',
            '',
            [],
            ErrorReplayRequest::class
        );

        try {
            $printer->validateJob();
            $this->fail('Expected IppStatusException was not thrown.');
        } catch (IppStatusException $e) {
            $response = $e->getResponse();
            // Unsupported attributes should be present in the response
            $this->assertNotNull($response->unsupportedAttributes);
            $this->assertNotEmpty($response->unsupportedAttributes);
        }
    }

    public static function errorFixtureProvider(): array
    {
        $fixtures = glob(__DIR__ . '/fixtures/real/*/PDF/validate-job-unsupported-format.meta.json') ?: [];
        $data = [];
        foreach ($fixtures as $path) {
            $label = basename(dirname(dirname($path))) . '/' . basename(dirname($path));
            $data[$label] = [$path];
        }
        return $data;
    }
}
