<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class RecordedRealOperationRequestTest extends TestCase
{
    public static function printJobFixtureProvider(): array
    {
        return self::fixtureProvider('print-job-held');
    }

    public static function getJobAttributesFixtureProvider(): array
    {
        return self::fixtureProvider('get-job-attributes');
    }

    public static function getJobAttributesByJobUriFixtureProvider(): array
    {
        $fixtures = self::fixtureProvider('get-job-attributes-by-job-uri');

        return $fixtures === [] ? ['pending recording' => [null]] : $fixtures;
    }

    public static function cancelJobAuthenticatedFixtureProvider(): array
    {
        return self::fixtureProvider('cancel-job-authenticated');
    }

    /**
     * @dataProvider printJobFixtureProvider
     */
    public function testRecordedPrintJobRequestsCarryRequiredTargetAndDocumentData(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $response = $fixture['response'];

        $this->assertSame(\obray\ipp\types\Operation::PRINT_JOB, $request['header']['operation']);
        $this->assertSame(['attributes-charset', 'attributes-natural-language'], array_slice($request['operation_attribute_names'], 0, 2));
        $this->assertContains('printer-uri', $request['operation_attribute_names']);
        $this->assertContains('document-format', $request['operation_attribute_names']);
        $this->assertContains('job-name', $request['operation_attribute_names']);
        $this->assertSame($fixture['meta']['printer_uri'], (string) $request['operation_attributes']->{'printer-uri'});

        $this->assertInstanceOf(\obray\ipp\JobAttributes::class, $request['job_attributes']);
        $this->assertTrue($request['job_attributes']->has('job-hold-until'));
        $this->assertNotSame('', $request['document']);

        $jobGroup = $this->firstGroup($response->jobAttributes);
        $this->assertNotNull($jobGroup);
        $this->assertTrue($jobGroup->has('job-id'));
        $this->assertTrue($jobGroup->has('job-state'));
    }

    /**
     * @dataProvider getJobAttributesFixtureProvider
     */
    public function testRecordedGetJobAttributesRequestsCarryRequiredJobTargetAttributes(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $response = $fixture['response'];

        $this->assertSame(\obray\ipp\types\Operation::GET_JOB_ATTRIBUTES, $request['header']['operation']);
        $this->assertSame(['attributes-charset', 'attributes-natural-language'], array_slice($request['operation_attribute_names'], 0, 2));
        $this->assertContains('printer-uri', $request['operation_attribute_names']);
        $this->assertContains('job-id', $request['operation_attribute_names']);
        $this->assertContains('requested-attributes', $request['operation_attribute_names']);
        $this->assertSame(
            \obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributeNames(),
            $this->attributeValues($request['operation_attributes']->{'requested-attributes'})
        );
        $this->assertSame('', $request['document']);

        $jobGroup = $this->firstGroup($response->jobAttributes);
        $this->assertNotNull($jobGroup);
        $this->assertTrue($jobGroup->has('job-id'));
        $this->assertTrue($jobGroup->has('job-state'));
    }

    /**
     * @dataProvider getJobAttributesByJobUriFixtureProvider
     */
    public function testRecordedGetJobAttributesByJobUriRequestsCarryJobUriTargetForm(?string $metaPath): void
    {
        if ($metaPath === null) {
            $this->markTestSkipped('No recorded job-uri fixtures exist yet. Run `composer record:fixtures` on a machine with reachable printers.');
        }

        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $response = $fixture['response'];

        $this->assertSame(\obray\ipp\types\Operation::GET_JOB_ATTRIBUTES, $request['header']['operation']);
        $this->assertSame(['attributes-charset', 'attributes-natural-language', 'job-uri'], array_slice($request['operation_attribute_names'], 0, 3));
        $this->assertContains('job-uri', $request['operation_attribute_names']);
        $this->assertNotContains('printer-uri', $request['operation_attribute_names']);
        $this->assertNotContains('job-id', $request['operation_attribute_names']);
        $this->assertContains('requested-attributes', $request['operation_attribute_names']);
        $this->assertSame($fixture['meta']['request']['job_uri'], (string) $request['operation_attributes']->{'job-uri'});
        $this->assertSame(
            \obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributeNames(),
            $this->attributeValues($request['operation_attributes']->{'requested-attributes'})
        );
        $this->assertSame('', $request['document']);

        $jobGroup = $this->firstGroup($response->jobAttributes);
        $this->assertNotNull($jobGroup);
        $this->assertTrue($jobGroup->has('job-id'));
        $this->assertTrue($jobGroup->has('job-state'));
    }

    /**
     * @dataProvider cancelJobAuthenticatedFixtureProvider
     */
    public function testRecordedCancelJobRequestsCarryRequiredJobTargetAttributes(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $response = $fixture['response'];

        $this->assertSame(\obray\ipp\types\Operation::CANCEL_JOB, $request['header']['operation']);
        $this->assertSame(['attributes-charset', 'attributes-natural-language'], array_slice($request['operation_attribute_names'], 0, 2));
        $this->assertContains('printer-uri', $request['operation_attribute_names']);
        $this->assertContains('job-id', $request['operation_attribute_names']);
        $this->assertContains('requesting-user-name', $request['operation_attribute_names']);
        $this->assertSame('', $request['document']);
        $this->assertSame('successful-ok', (string) $response->statusCode);
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
        $response = new \obray\ipp\transport\IPPPayload();
        $response->decode($responseBinary);

        return [
            'meta' => $meta,
            'request' => $this->decodeRequestPayload($requestBinary),
            'response' => $response,
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
            $nextTag = $jobAttributes->decode($binary, $offset);
        }

        $document = '';
        if (isset($binary[$offset]) && ord($binary[$offset]) === 0x03) {
            $offset++;
            $document = substr($binary, $offset);
        }

        return [
            'header' => $header,
            'operation_attributes' => $operationAttributes,
            'operation_attribute_names' => array_keys($operationAttributes->jsonSerialize()),
            'job_attributes' => $jobAttributes,
            'document' => $document,
        ];
    }

    private function firstGroup($groups): ?\obray\ipp\AttributeGroup
    {
        if ($groups instanceof \obray\ipp\AttributeGroup) {
            return $groups;
        }

        if (!is_array($groups) || $groups === []) {
            return null;
        }

        $first = reset($groups);

        return $first instanceof \obray\ipp\AttributeGroup ? $first : null;
    }

    private function attributeValues($attribute): array
    {
        if (!is_array($attribute)) {
            return [(string) $attribute];
        }

        return array_map(static fn ($value) => (string) $value, $attribute);
    }
}
