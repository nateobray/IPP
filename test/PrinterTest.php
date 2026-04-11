<?php
$loader = require_once 'vendor/autoload.php';

use obray\ipp\test\FakeRequest;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    protected \obray\ipp\Printer $printer;

    protected function setUp(): void
    {
        FakeRequest::reset();

        $this->printer = (new \obray\ipp\Printer(
            'ipp://localhost/printers/CUPS_PDF',
            'demo-user',
            'secret',
            [['key' => CURLOPT_TIMEOUT, 'value' => 5]]
        ))->setRequestClass(FakeRequest::class);
    }

    public function testPrintJobBuildsExpectedPayload(): void
    {
        $response = $this->printer->printJob('Hello world', 1234, [
            'document-format' => 'application/pdf',
            'sides' => 'two-sided-long-edge',
        ]);

        $this->assertInstanceOf(\obray\ipp\transport\IPPPayload::class, $response);
        $this->assertSame(1234, $response->requestId->getValue());
        $this->assertSame('successful-ok', (string) $response->statusCode);

        $this->assertSame('ipp://localhost/printers/CUPS_PDF', FakeRequest::$lastCall['printerURI']);
        $this->assertSame('demo-user', FakeRequest::$lastCall['user']);
        $this->assertSame('secret', FakeRequest::$lastCall['password']);
        $this->assertSame([['key' => CURLOPT_TIMEOUT, 'value' => 5]], FakeRequest::$lastCall['curlOptions']);
        $this->assertSame('1.1', FakeRequest::$lastCall['version']);
        $this->assertSame(\obray\ipp\types\Operation::PRINT_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('application/pdf', (string) FakeRequest::$lastCall['operationAttributes']->{'document-format'});
        $this->assertSame('two-sided-long-edge', (string) FakeRequest::$lastCall['jobAttributes']->{'sides'});
        $this->assertSame('Hello world', FakeRequest::$lastCall['document']);
    }

    public function testPrintURIBuildsExpectedPayload(): void
    {
        $this->printer->printURI('https://example.test/file.pdf', 22, [
            'job-name' => 'Remote document',
        ]);

        $this->assertSame(\obray\ipp\types\Operation::PRINT_URI, FakeRequest::$lastCall['operation']);
        $this->assertSame(22, FakeRequest::$lastCall['requestId']);
        $this->assertSame('https://example.test/file.pdf', (string) FakeRequest::$lastCall['operationAttributes']->{'document-uri'});
        $this->assertSame('Remote document', (string) FakeRequest::$lastCall['operationAttributes']->{'job-name'});
        $this->assertSame('', FakeRequest::$lastCall['document']);
    }

    public function testValidateJobIncludesJobAttributes(): void
    {
        $this->printer->validateJob(4987, [
            'document-format' => 'application/postscript',
            'media' => 'iso_a4_210x297mm',
        ]);

        $this->assertSame(\obray\ipp\types\Operation::VALIDATE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('application/postscript', (string) FakeRequest::$lastCall['operationAttributes']->{'document-format'});
        $this->assertSame('iso_a4_210x297mm', (string) FakeRequest::$lastCall['jobAttributes']->{'media'});
    }

    public function testCreateJobBuildsExpectedPayload(): void
    {
        $this->printer->createJob(77, [
            'job-name' => 'Batch print',
            'sides' => 'one-sided',
        ]);

        $this->assertSame(\obray\ipp\types\Operation::CREATE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('Batch print', (string) FakeRequest::$lastCall['operationAttributes']->{'job-name'});
        $this->assertSame('one-sided', (string) FakeRequest::$lastCall['jobAttributes']->{'sides'});
    }

    public function testCreateJobRejectsDocumentOperationAttributes(): void
    {
        try {
            $this->printer->createJob(78, [
                'document-format' => 'application/pdf',
            ]);
            $this->fail('Expected Create-Job validation to reject document-format.');
        } catch (\obray\ipp\exceptions\InvalidRequest $exception) {
            $this->assertSame([], FakeRequest::$lastCall);
            $this->assertStringContainsString('Create-Job forbids operation attribute "document-format"', $exception->getMessage());
        }
    }

    public function testGetPrinterAttributesBuildsExpectedPayload(): void
    {
        $this->printer->getPrinterAttributes(567);

        $this->assertSame(\obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame('1.1', FakeRequest::$lastCall['version']);
        $this->assertSame(567, FakeRequest::$lastCall['requestId']);
    }

    public function testGetPrinterAttributesSupportsRequestedAttributes(): void
    {
        $this->printer->getPrinterAttributes(568, ['printer-name', 'printer-state']);

        $this->assertIsArray(FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'});
        $this->assertSame('printer-name', (string) FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'}[0]);
        $this->assertSame('printer-state', (string) FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'}[1]);
    }

    public function testGetPrinterAttributesSupportsDocumentFormatFilter(): void
    {
        $this->printer->getPrinterAttributes(569, ['document-format-supported'], 'application/pdf');

        $this->assertSame('application/pdf', (string) FakeRequest::$lastCall['operationAttributes']->{'document-format'});
    }

    public function testGetJobsBuildsExpectedPayload(): void
    {
        $this->printer->getJobs(375, 'completed', 10, true);

        $this->assertSame(\obray\ipp\types\Operation::GET_JOBS, FakeRequest::$lastCall['operation']);
        $this->assertSame('1.1', FakeRequest::$lastCall['version']);
        $this->assertSame('completed', (string) FakeRequest::$lastCall['operationAttributes']->{'which-jobs'});
        $this->assertSame('10', (string) FakeRequest::$lastCall['operationAttributes']->{'limit'});
        $this->assertSame('true', (string) FakeRequest::$lastCall['operationAttributes']->{'my-jobs'});
    }

    public function testGetJobsSupportsRequestedAttributes(): void
    {
        $this->printer->getJobs(376, 'completed', 5, false, ['job-id', 'job-state']);

        $this->assertIsArray(FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'});
        $this->assertSame('job-id', (string) FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'}[0]);
        $this->assertSame('job-state', (string) FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'}[1]);
    }

    public function testPausePrinterBuildsExpectedPayload(): void
    {
        $this->printer->pausePrinter(11);

        $this->assertSame(\obray\ipp\types\Operation::PAUSE_PRINTER, FakeRequest::$lastCall['operation']);
        $this->assertSame(11, FakeRequest::$lastCall['requestId']);
    }

    public function testResumePrinterBuildsExpectedPayload(): void
    {
        $this->printer->resumePrinter(12);

        $this->assertSame(\obray\ipp\types\Operation::RESUME_PRINTER, FakeRequest::$lastCall['operation']);
        $this->assertSame(12, FakeRequest::$lastCall['requestId']);
    }

    public function testPurgeJobsBuildsExpectedPayload(): void
    {
        $this->printer->purgeJobs(13);

        $this->assertSame(\obray\ipp\types\Operation::PURGE_JOBS, FakeRequest::$lastCall['operation']);
        $this->assertSame(13, FakeRequest::$lastCall['requestId']);
    }

    public function testValidateJobRejectsUnsupportedCharsetBeforeSend(): void
    {
        try {
            $this->printer->validateJob(14, [
                'attributes-charset' => 'us-ascii',
            ]);
            $this->fail('Expected Validate-Job validation to reject non-utf-8 charsets.');
        } catch (\obray\ipp\exceptions\ClientErrorCharsetNotSupported $exception) {
            $this->assertSame([], FakeRequest::$lastCall);
        }
    }

    public function testPrintUriRejectsEmptyDocumentUri(): void
    {
        try {
            $this->printer->printURI('', 15);
            $this->fail('Expected Print-URI validation to reject an empty document-uri.');
        } catch (\obray\ipp\exceptions\InvalidRequest $exception) {
            $this->assertSame([], FakeRequest::$lastCall);
            $this->assertStringContainsString('Print-URI requires operation attribute "document-uri"', $exception->getMessage());
        }
    }

    public function testSetPrinterAttributesBuildsExpectedPayload(): void
    {
        $this->printer->setPrinterAttributes(['printer-info' => 'My Printer'], 16);

        $this->assertSame(\obray\ipp\types\Operation::SET_PRINTER_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame(16, FakeRequest::$lastCall['requestId']);
        $this->assertSame('1.1', FakeRequest::$lastCall['version']);
        $this->assertNotNull(FakeRequest::$lastCall['printerAttributes']);
        $this->assertSame('My Printer', (string) FakeRequest::$lastCall['printerAttributes']->{'printer-info'});
    }
}
