<?php
$loader = require_once 'vendor/autoload.php';

use obray\ipp\test\FakeRequest;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    protected \obray\ipp\Document $document;

    protected function setUp(): void
    {
        FakeRequest::reset();

        $this->document = (new \obray\ipp\Document(
            'ipp://localhost/printers/CUPS_PDF',
            42,
            1,
            'demo-user',
            'secret'
        ))->setRequestClass(FakeRequest::class);
    }

    // ── Get-Document-Attributes ──────────────────────────────────────────

    public function testGetDocumentAttributesBuildsExpectedPayload(): void
    {
        $this->document->getDocumentAttributes(100);

        $this->assertSame(\obray\ipp\types\Operation::GET_DOCUMENT_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame(100, FakeRequest::$lastCall['requestId']);
        $this->assertSame('2.0', FakeRequest::$lastCall['version']);
        $this->assertSame('ipp://localhost/printers/CUPS_PDF', (string) FakeRequest::$lastCall['operationAttributes']->{'printer-uri'});
        $this->assertSame('42', (string) FakeRequest::$lastCall['operationAttributes']->{'job-id'});
        $this->assertSame('1', (string) FakeRequest::$lastCall['operationAttributes']->{'document-number'});
        $this->assertSame('demo-user', (string) FakeRequest::$lastCall['operationAttributes']->{'requesting-user-name'});
    }

    public function testGetDocumentAttributesWithJobUriBuildsExpectedPayload(): void
    {
        $document = (new \obray\ipp\Document(
            'ipp://localhost/printers/CUPS_PDF',
            'ipp://localhost/jobs/42',
            2
        ))->setRequestClass(FakeRequest::class);

        $document->getDocumentAttributes(101);

        $this->assertSame(\obray\ipp\types\Operation::GET_DOCUMENT_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame('ipp://localhost/jobs/42', (string) FakeRequest::$lastCall['operationAttributes']->{'job-uri'});
        $this->assertSame('2', (string) FakeRequest::$lastCall['operationAttributes']->{'document-number'});
    }

    public function testGetDocumentAttributesWithRequestedAttributesBuildsExpectedPayload(): void
    {
        $this->document->getDocumentAttributes(102, ['document-name', 'document-state']);

        $requested = FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'};
        $this->assertIsArray($requested);
        $this->assertSame('document-name', (string) $requested[0]);
        $this->assertSame('document-state', (string) $requested[1]);
    }

    // ── Set-Document-Attributes ──────────────────────────────────────────

    public function testSetDocumentAttributesBuildsExpectedPayload(): void
    {
        $this->document->setDocumentAttributes(['document-name' => 'My Report', 'copies' => 2], 103);

        $this->assertSame(\obray\ipp\types\Operation::SET_DOCUMENT_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame(103, FakeRequest::$lastCall['requestId']);
        $this->assertSame('2.0', FakeRequest::$lastCall['version']);
        $this->assertNotNull(FakeRequest::$lastCall['documentAttributes']);
        $this->assertSame('2', (string) FakeRequest::$lastCall['documentAttributes']->{'copies'});
    }

    // ── Cancel-Document ──────────────────────────────────────────────────

    public function testCancelDocumentBuildsExpectedPayload(): void
    {
        $this->document->cancelDocument(104);

        $this->assertSame(\obray\ipp\types\Operation::CANCEL_DOCUMENT, FakeRequest::$lastCall['operation']);
        $this->assertSame(104, FakeRequest::$lastCall['requestId']);
        $this->assertSame('2.0', FakeRequest::$lastCall['version']);
        $this->assertSame('42', (string) FakeRequest::$lastCall['operationAttributes']->{'job-id'});
        $this->assertSame('1', (string) FakeRequest::$lastCall['operationAttributes']->{'document-number'});
    }

    // ── DocumentAttributes encode/decode round-trip ──────────────────────

    public function testDocumentAttributesEncodeDecodeRoundTrip(): void
    {
        $attrs = new \obray\ipp\DocumentAttributes();
        $attrs->{'document-number'}    = 3;
        $attrs->{'document-name'}      = 'Quarterly Report';
        $attrs->{'document-state'}     = \obray\ipp\enums\DocumentState::processing;
        $attrs->{'document-state-reasons'} = ['processing-to-stop-point', 'job-incoming'];
        $attrs->{'k-octets'}           = 128;
        $attrs->{'last-document'}      = false;

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 9001);
        $binary .= (new \obray\ipp\OperationAttributes())->encode();
        $binary .= $attrs->encode();
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertIsArray($payload->documentAttributes);
        $this->assertCount(1, $payload->documentAttributes);

        $decoded = $payload->documentAttributes[0];
        $this->assertSame('3', (string) $decoded->{'document-number'});
        $this->assertSame('Quarterly Report', (string) $decoded->{'document-name'});
        $this->assertSame('processing', (string) $decoded->{'document-state'});
        $this->assertIsArray($decoded->{'document-state-reasons'});
        $this->assertSame('processing-to-stop-point', (string) $decoded->{'document-state-reasons'}[0]);
        $this->assertSame('job-incoming', (string) $decoded->{'document-state-reasons'}[1]);
        $this->assertSame('128', (string) $decoded->{'k-octets'});
        $this->assertSame('false', (string) $decoded->{'last-document'});
    }

    public function testDocumentStateEnumValues(): void
    {
        $this->assertSame(3, \obray\ipp\enums\DocumentState::pending);
        $this->assertSame(5, \obray\ipp\enums\DocumentState::processing);
        $this->assertSame(6, \obray\ipp\enums\DocumentState::processing_stopped);
        $this->assertSame(7, \obray\ipp\enums\DocumentState::canceled);
        $this->assertSame(8, \obray\ipp\enums\DocumentState::aborted);
        $this->assertSame(9, \obray\ipp\enums\DocumentState::completed);
    }
}
