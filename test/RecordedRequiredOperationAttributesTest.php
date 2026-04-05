<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class RecordedRequiredOperationAttributesTest extends TestCase
{
    public static function fixtureProvider(): array
    {
        $matrix = self::operationMatrix();
        $data = [];

        foreach ($matrix as $operation => $requirements) {
            $fixtureFiles = glob(__DIR__ . '/fixtures/real/*/*/' . $operation . '.meta.json') ?: [];
            sort($fixtureFiles);

            foreach ($fixtureFiles as $fixtureFile) {
                $data[basename(dirname($fixtureFile)) . ' ' . $operation] = [$fixtureFile, $requirements];
            }
        }

        return $data;
    }

    /**
     * @dataProvider fixtureProvider
     */
    public function testRecordedRequestsMeetRequiredOperationAttributeMatrix(string $metaPath, array $requirements): void
    {
        $fixture = $this->loadFixture($metaPath);
        $request = $fixture['request'];
        $attributeNames = array_keys($request['operation_attributes']->jsonSerialize());

        $this->assertSame($requirements['operation'], $request['header']['operation']);

        $expectedLeadingAttributes = array_merge(
            ['attributes-charset', 'attributes-natural-language'],
            $requirements['required_target_attributes']
        );
        $this->assertSame(
            $expectedLeadingAttributes,
            array_slice($attributeNames, 0, count($expectedLeadingAttributes)),
            sprintf('Unexpected leading operation attribute order in %s', $metaPath)
        );

        foreach ($requirements['required_target_attributes'] as $attributeName) {
            $this->assertTrue(
                $request['operation_attributes']->has($attributeName),
                sprintf('Missing required target attribute "%s" in %s', $attributeName, $metaPath)
            );
        }

        foreach ($requirements['required_operation_attributes'] as $attributeName) {
            $this->assertTrue(
                $request['operation_attributes']->has($attributeName),
                sprintf('Missing required operation attribute "%s" in %s', $attributeName, $metaPath)
            );
        }

        foreach ($requirements['required_job_attributes'] as $attributeName) {
            $this->assertInstanceOf(
                \obray\ipp\JobAttributes::class,
                $request['job_attributes'],
                sprintf('Expected job attributes group in %s', $metaPath)
            );
            $this->assertTrue(
                $request['job_attributes']->has($attributeName),
                sprintf('Missing required job attribute "%s" in %s', $attributeName, $metaPath)
            );
        }
    }

    private static function operationMatrix(): array
    {
        return [
            'get-printer-attributes' => [
                'operation' => \obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES,
                'required_target_attributes' => ['printer-uri'],
                'required_operation_attributes' => ['requested-attributes'],
                'required_job_attributes' => [],
            ],
            'validate-job' => [
                'operation' => \obray\ipp\types\Operation::VALIDATE_JOB,
                'required_target_attributes' => ['printer-uri'],
                'required_operation_attributes' => ['document-format'],
                'required_job_attributes' => ['job-hold-until'],
            ],
            'validate-job-unsupported-format' => [
                'operation' => \obray\ipp\types\Operation::VALIDATE_JOB,
                'required_target_attributes' => ['printer-uri'],
                'required_operation_attributes' => ['document-format'],
                'required_job_attributes' => [],
            ],
            'get-jobs' => [
                'operation' => \obray\ipp\types\Operation::GET_JOBS,
                'required_target_attributes' => ['printer-uri'],
                'required_operation_attributes' => ['which-jobs', 'limit', 'my-jobs', 'requested-attributes'],
                'required_job_attributes' => [],
            ],
            'print-job-held' => [
                'operation' => \obray\ipp\types\Operation::PRINT_JOB,
                'required_target_attributes' => ['printer-uri'],
                'required_operation_attributes' => ['document-format', 'job-name'],
                'required_job_attributes' => ['job-hold-until'],
            ],
            'get-job-attributes' => [
                'operation' => \obray\ipp\types\Operation::GET_JOB_ATTRIBUTES,
                'required_target_attributes' => ['printer-uri', 'job-id'],
                'required_operation_attributes' => ['requested-attributes'],
                'required_job_attributes' => [],
            ],
            'get-job-attributes-by-job-uri' => [
                'operation' => \obray\ipp\types\Operation::GET_JOB_ATTRIBUTES,
                'required_target_attributes' => ['job-uri'],
                'required_operation_attributes' => ['requested-attributes'],
                'required_job_attributes' => [],
            ],
            'cancel-job-authenticated' => [
                'operation' => \obray\ipp\types\Operation::CANCEL_JOB,
                'required_target_attributes' => ['printer-uri', 'job-id'],
                'required_operation_attributes' => ['requesting-user-name'],
                'required_job_attributes' => [],
            ],
            'cancel-job-unauthenticated' => [
                'operation' => \obray\ipp\types\Operation::CANCEL_JOB,
                'required_target_attributes' => ['printer-uri', 'job-id'],
                'required_operation_attributes' => [],
                'required_job_attributes' => [],
            ],
        ];
    }

    private function loadFixture(string $metaPath): array
    {
        $meta = json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
        $requestBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['request_file']);

        return [
            'meta' => $meta,
            'request' => $this->decodeRequestPayload($requestBinary),
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
}
