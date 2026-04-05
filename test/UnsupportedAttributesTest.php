<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class UnsupportedAttributesTest extends TestCase
{
    public function testUnsupportedAttributesCanEncodeAttributeInstances(): void
    {
        $unsupportedAttributes = new \obray\ipp\UnsupportedAttributes();
        $unsupportedAttributes->set(
            'job-template',
            new \obray\ipp\Attribute('job-template', 'unsupported-value', \obray\ipp\enums\Types::KEYWORD)
        );

        $encoded = $unsupportedAttributes->encode();
        $decoded = new \obray\ipp\UnsupportedAttributes();
        $offset = 0;
        $decoded->decode($encoded, $offset);

        $this->assertTrue($decoded->has('job-template'));
        $this->assertSame('unsupported-value', (string) $decoded->{'job-template'});
    }

    public function testPayloadDecodeIncludesUnsupportedAttributesGroup(): void
    {
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::PRINT_JOB),
            new \obray\ipp\types\Integer(44),
            null,
            new \obray\ipp\OperationAttributes(),
            null,
            null,
            $this->buildUnsupportedAttributesGroup()
        );

        $response = new \obray\ipp\transport\IPPPayload();
        $response->decode($this->convertRequestPayloadToResponsePayload($payload->encode()));

        $this->assertIsArray($response->unsupportedAttributes);
        $this->assertCount(1, $response->unsupportedAttributes);
        $this->assertSame('unsupported-value', (string) $response->unsupportedAttributes[0]->{'job-template'});
    }

    private function buildUnsupportedAttributesGroup(): \obray\ipp\UnsupportedAttributes
    {
        $unsupportedAttributes = new \obray\ipp\UnsupportedAttributes();
        $unsupportedAttributes->set(
            'job-template',
            new \obray\ipp\Attribute('job-template', 'unsupported-value', \obray\ipp\enums\Types::KEYWORD)
        );

        return $unsupportedAttributes;
    }

    private function convertRequestPayloadToResponsePayload(string $binary): string
    {
        return substr($binary, 0, 2)
            . pack('n', \obray\ipp\types\StatusCode::successful_ok)
            . substr($binary, 4);
    }
}
