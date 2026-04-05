<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class RecordedRealComplianceTest extends TestCase
{
    private const REQUIRED_RFC2911_OPERATIONS = [
        'print-job',
        'validate-job',
        'cancel-job',
        'get-job-attributes',
        'get-jobs',
        'get-printer-attributes',
    ];

    public static function getPrinterAttributesFixtureProvider(): array
    {
        return self::fixtureProvider('get-printer-attributes');
    }

    public static function validateJobFixtureProvider(): array
    {
        return self::fixtureProvider('validate-job');
    }

    public static function getJobsFixtureProvider(): array
    {
        return self::fixtureProvider('get-jobs');
    }

    /**
     * @dataProvider getPrinterAttributesFixtureProvider
     */
    public function testRecordedGetPrinterAttributesFixturesMeetCorePrinterRequirements(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $response = $fixture['response'];

        $this->assertSame(1, $request['header']['major']);
        $this->assertSame(1, $request['header']['minor']);
        $this->assertSame(\obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES, $request['header']['operation']);

        $operationAttributeNames = array_keys($request['operation_attributes']->jsonSerialize());
        $this->assertSame(['attributes-charset', 'attributes-natural-language'], array_slice($operationAttributeNames, 0, 2));
        $this->assertContains('printer-uri', $operationAttributeNames);
        $this->assertContains('requested-attributes', $operationAttributeNames);
        $this->assertSame($fixture['meta']['printer_uri'], (string) $request['operation_attributes']->{'printer-uri'});

        $requestedAttributes = $this->attributeValues($request['operation_attributes']->{'requested-attributes'});
        $this->assertSame(\obray\ipp\spec\Rfc2911AttributeMatrix::requiredPrinterDescriptionAttributeNames(), $requestedAttributes);

        $this->assertStatusIsSuccessful($response);
        $this->assertSame('utf-8', (string) $response->operationAttributes->{'attributes-charset'});
        $this->assertNotSame('', (string) $response->operationAttributes->{'attributes-natural-language'});

        $printerAttributes = $this->firstGroup($response->printerAttributes);
        $this->assertNotNull($printerAttributes);

        foreach (\obray\ipp\spec\Rfc2911AttributeMatrix::requiredPrinterDescriptionAttributeNames() as $attributeName) {
            $this->assertTrue(
                $printerAttributes->has($attributeName),
                sprintf('Expected attribute "%s" in %s', $attributeName, $metaPath)
            );
        }

        $operationsSupported = $this->attributeValues($printerAttributes->{'operations-supported'});
        foreach (self::REQUIRED_RFC2911_OPERATIONS as $operation) {
            $this->assertContains(
                $operation,
                $operationsSupported,
                sprintf('Expected operation "%s" in %s', $operation, $metaPath)
            );
        }

        $ippVersionsSupported = $this->attributeValues($printerAttributes->{'ipp-versions-supported'});
        $this->assertContains('1.1', $ippVersionsSupported);

        $documentFormatDefault = (string) $printerAttributes->{'document-format-default'};
        $documentFormatsSupported = $this->attributeValues($printerAttributes->{'document-format-supported'});
        $this->assertContains($documentFormatDefault, $documentFormatsSupported);

        $charsetConfigured = (string) $printerAttributes->{'charset-configured'};
        $charsetSupported = $this->attributeValues($printerAttributes->{'charset-supported'});
        $this->assertContains($charsetConfigured, $charsetSupported);

        $naturalLanguageConfigured = (string) $printerAttributes->{'natural-language-configured'};
        $generatedLanguages = $this->attributeValues($printerAttributes->{'generated-natural-language-supported'});
        $this->assertContains($naturalLanguageConfigured, $generatedLanguages);

        $printerUriSupported = $this->attributeValues($printerAttributes->{'printer-uri-supported'});
        $uriAuthenticationSupported = $this->attributeValues($printerAttributes->{'uri-authentication-supported'});
        $uriSecuritySupported = $this->attributeValues($printerAttributes->{'uri-security-supported'});
        $this->assertCount(count($printerUriSupported), $uriAuthenticationSupported);
        $this->assertCount(count($printerUriSupported), $uriSecuritySupported);

        $this->assertContains((string) $printerAttributes->{'printer-is-accepting-jobs'}, ['true', 'false']);
        $this->assertMatchesRegularExpression('/^\d+$/', (string) $printerAttributes->{'queued-job-count'});
        $this->assertNotEmpty($this->attributeValues($printerAttributes->{'compression-supported'}));
    }

    /**
     * @dataProvider validateJobFixtureProvider
     */
    public function testRecordedValidateJobFixturesPreserveRequestSeparationAndSuccessSemantics(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $response = $fixture['response'];

        $this->assertSame(\obray\ipp\types\Operation::VALIDATE_JOB, $request['header']['operation']);

        $operationAttributeNames = array_keys($request['operation_attributes']->jsonSerialize());
        $this->assertSame(['attributes-charset', 'attributes-natural-language'], array_slice($operationAttributeNames, 0, 2));
        $this->assertContains('printer-uri', $operationAttributeNames);
        $this->assertContains('document-format', $operationAttributeNames);

        $jobAttributes = $request['job_attributes'];
        $this->assertInstanceOf(\obray\ipp\JobAttributes::class, $jobAttributes);
        $this->assertTrue($jobAttributes->has('job-hold-until'));

        $this->assertStatusIsSuccessful($response);
        $this->assertSame('utf-8', (string) $response->operationAttributes->{'attributes-charset'});
        $this->assertNotSame('', (string) $response->operationAttributes->{'attributes-natural-language'});

        if ((string) $response->statusCode === 'successful-ok-ignored-or-substituted-attributes') {
            $unsupportedGroup = $this->firstGroup($response->unsupportedAttributes);
            $this->assertNotNull($unsupportedGroup);

            foreach (array_keys($unsupportedGroup->jsonSerialize()) as $unsupportedAttributeName) {
                $this->assertContains($unsupportedAttributeName, ['job-hold-until']);
            }
        }
    }

    /**
     * @dataProvider getJobsFixtureProvider
     */
    public function testRecordedGetJobsFixturesShowStableRequestAndResponseSemantics(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $response = $fixture['response'];

        $this->assertSame(\obray\ipp\types\Operation::GET_JOBS, $request['header']['operation']);

        $operationAttributes = $request['operation_attributes'];
        $operationAttributeNames = array_keys($operationAttributes->jsonSerialize());
        $this->assertSame(['attributes-charset', 'attributes-natural-language'], array_slice($operationAttributeNames, 0, 2));
        $this->assertContains('printer-uri', $operationAttributeNames);
        $this->assertSame('not-completed', (string) $operationAttributes->{'which-jobs'});
        $this->assertSame('5', (string) $operationAttributes->{'limit'});
        $this->assertSame('false', (string) $operationAttributes->{'my-jobs'});
        $this->assertSame(['job-id', 'job-name', 'job-state'], $this->attributeValues($operationAttributes->{'requested-attributes'}));

        $this->assertStatusIsSuccessful($response);
        $this->assertSame('utf-8', (string) $response->operationAttributes->{'attributes-charset'});
        $this->assertNotSame('', (string) $response->operationAttributes->{'attributes-natural-language'});

        $jobGroups = $this->normalizeGroups($response->jobAttributes);
        foreach ($jobGroups as $jobGroup) {
            $this->assertTrue(
                $jobGroup->has('job-id') || $jobGroup->has('job-name') || $jobGroup->has('job-state')
            );
        }
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

    private function loadFixture(string $metaPath): array
    {
        $meta = json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
        $requestBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['request_file']);
        $responseBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['response_file']);

        $responsePayload = new \obray\ipp\transport\IPPPayload();
        $responsePayload->decode($responseBinary);

        return [
            'meta' => $meta,
            'request' => $this->decodeRequestPayload($requestBinary),
            'response' => $responsePayload,
        ];
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

    private function assertStatusIsSuccessful(\obray\ipp\transport\IPPPayload $payload): void
    {
        $status = (string) $payload->statusCode;

        $this->assertTrue(
            str_starts_with($status, 'successful-ok'),
            sprintf('Expected a successful IPP status, got "%s".', $status)
        );
    }

    private function firstGroup($groups): ?\obray\ipp\AttributeGroup
    {
        $normalizedGroups = $this->normalizeGroups($groups);

        return $normalizedGroups[0] ?? null;
    }

    private function normalizeGroups($groups): array
    {
        if ($groups instanceof \obray\ipp\AttributeGroup) {
            return [$groups];
        }

        if (!is_array($groups)) {
            return [];
        }

        return array_values(array_filter(
            $groups,
            static fn ($group) => $group instanceof \obray\ipp\AttributeGroup
        ));
    }

    private function attributeValues($attribute): array
    {
        if (!is_array($attribute)) {
            return [(string) $attribute];
        }

        return array_map(static fn ($value) => (string) $value, $attribute);
    }
}
