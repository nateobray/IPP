<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class RecordedRealErrorPathTest extends TestCase
{
    public static function unsupportedFormatFixtureProvider(): array
    {
        return self::fixtureProvider('validate-job-unsupported-format');
    }

    public static function unauthenticatedCancelFixtureProvider(): array
    {
        return self::fixtureProvider('cancel-job-unauthenticated');
    }

    /**
     * @dataProvider unsupportedFormatFixtureProvider
     */
    public function testRecordedUnsupportedFormatFixturesCaptureRealClientErrorResponses(string $metaPath): void
    {
        $meta = $this->loadMeta($metaPath);
        $request = $this->decodeRequestPayload((string) file_get_contents(dirname($metaPath) . '/' . $meta['request_file']));
        $response = new \obray\ipp\transport\IPPPayload();
        $response->decode((string) file_get_contents(dirname($metaPath) . '/' . $meta['response_file']));

        $this->assertSame('ipp-response', $meta['kind'] ?? 'ipp-response');
        $this->assertSame(\obray\ipp\types\Operation::VALIDATE_JOB, $request['header']['operation']);
        $this->assertSame('application/x-obray-ipp-compliance-probe', (string) $request['operation_attributes']->{'document-format'});

        $status = (string) $response->statusCode;
        $this->assertContains(
            $status,
            [
                'client-error-document-format-not-supported',
                'client-error-attributes-or-values-not-supported',
            ]
        );
        $this->assertSame('client-error', $response->statusCode->getClass());
    }

    /**
     * @dataProvider unauthenticatedCancelFixtureProvider
     */
    public function testRecordedUnauthenticatedCancelFixturesCaptureHttpAuthChallenges(string $metaPath): void
    {
        $meta = $this->loadMeta($metaPath);
        $request = $this->decodeRequestPayload((string) file_get_contents(dirname($metaPath) . '/' . $meta['request_file']));
        $responsePath = dirname($metaPath) . '/' . $meta['response_file'];

        $this->assertSame('http-error', $meta['kind'] ?? null);
        $this->assertSame(401, $meta['http']['status']);
        $this->assertSame(\obray\ipp\exceptions\AuthenticationError::class, $meta['error']['class']);
        $this->assertSame(\obray\ipp\types\Operation::CANCEL_JOB, $request['header']['operation']);

        $operationAttributes = $request['operation_attributes'];
        $this->assertTrue($operationAttributes->has('printer-uri'));
        $this->assertTrue($operationAttributes->has('job-id'));
        $this->assertFalse($operationAttributes->has('requesting-user-name'));
        $this->assertFileExists($responsePath);
    }

    private static function fixtureProvider(string $operation): array
    {
        $fixtureFiles = glob(__DIR__ . '/fixtures/real/*/*/' . $operation . '.meta.json') ?: [];
        sort($fixtureFiles);

        $data = [];
        foreach ($fixtureFiles as $fixtureFile) {
            $data[basename(dirname($fixtureFile)) . ' ' . $operation] = [$fixtureFile];
        }

        return $data;
    }

    private function loadMeta(string $metaPath): array
    {
        return json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
    }

    private function decodeRequestPayload(string $binary): array
    {
        $header = unpack('Cmajor/Cminor/noperation/Nrequest_id', $binary);
        $offset = 8;

        $operationAttributes = new \obray\ipp\OperationAttributes();
        $nextTag = $operationAttributes->decode($binary, $offset);

        $jobAttributes = null;
        if ($nextTag === 0x02) {
            $jobAttributes = new \obray\ipp\JobAttributes();
            $jobAttributes->decode($binary, $offset);
        }

        return [
            'header' => $header,
            'operation_attributes' => $operationAttributes,
            'job_attributes' => $jobAttributes,
        ];
    }
}
