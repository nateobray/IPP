<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    protected $printer;

    /**
     * Set Up
     * PHPUnit setup fixture
     */

    protected function setUp(): void
    {
        $this->printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF');
    }

    /**
     * Test Print Job
     * Prints to the specified printer and tests if the response is what expected
     */

    public function testPrintJob()
    {
        $IPPPayload = $this->printer->printJob('Hello world', 1234);
        // test if IPPPayload is properly structured
        $this->assertInstanceOf(\obray\ipp\transport\IPPPayload::class, $IPPPayload);
        $this->assertInstanceOf(\obray\ipp\types\VersionNumber::class, $IPPPayload->versionNumber);
        $this->assertInstanceOf(\obray\ipp\types\Integer::class, $IPPPayload->requestId);
        $this->assertSame(1234, $IPPPayload->requestId->getValue());
        $this->assertInstanceOf(\obray\ipp\types\StatusCode::class, $IPPPayload->statusCode);
        $this->assertInstanceOf(\obray\ipp\OperationAttributes::class, $IPPPayload->operationAttributes);
        $this->assertInstanceOf(\obray\ipp\JobDescriptionAttributes::class, $IPPPayload->jobDescriptionAttributes);

        // test if it was scuccessful
        $this->assertSame('successful-ok', (string)$IPPPayload->statusCode);
    }

    /**
     * Test Validate Print Job
     */

    public function testValidatePrintJob()
    {
        $IPPPayload = $this->printer->validateJob(4987);
        $this->assertInstanceOf(\obray\ipp\transport\IPPPayload::class, $IPPPayload);
        $this->assertInstanceOf(\obray\ipp\types\VersionNumber::class, $IPPPayload->versionNumber);
        $this->assertInstanceOf(\obray\ipp\types\Integer::class, $IPPPayload->requestId);
        $this->assertSame(4987, $IPPPayload->requestId->getValue());
        $this->assertInstanceOf(\obray\ipp\types\StatusCode::class, $IPPPayload->statusCode);
        $this->assertInstanceOf(\obray\ipp\OperationAttributes::class, $IPPPayload->operationAttributes);

        // test if it was scuccessful
        $this->assertSame('successful-ok', (string)$IPPPayload->statusCode);
    }

    /**
     * Test Validate Print Job when printer not found
     */

    public function testPrintJobPrinterNotFound()
    {
        // get bogus printer object
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/printer-no-exist');

        // do bogus print call
        $response = $printer->printJob('Hello world',1234);

        // check if response was good
        $this->assertInstanceOf(\obray\ipp\transport\IPPPayload::class, $response);
        $this->assertSame('1.1', (string)$response->versionNumber);
        $this->assertSame('1234', (string)$response->requestId);
        $this->assertSame('client-error-not-found', (string)$response->statusCode);
        $this->assertSame('The printer or class does not exist.', (string)$response->operationAttributes->{'status-message'});
        $this->assertEmpty($response->jobDescriptionAttributes);
        $this->assertEmpty($response->printerDescriptionAttributes);
    }

    /**
     * Test Get Printer attributes
     */

    public function testGetPrinterAttributes()
    {
        // do test printer call
        $IPPPayload = $this->printer->getPrinterAttributes(567);

        // test if IPPPayload is properly structured
        $this->assertInstanceOf(\obray\ipp\transport\IPPPayload::class, $IPPPayload);
        $this->assertInstanceOf(\obray\ipp\types\VersionNumber::class, $IPPPayload->versionNumber);
        $this->assertInstanceOf(\obray\ipp\types\Integer::class, $IPPPayload->requestId);
        $this->assertSame(567, $IPPPayload->requestId->getValue());
        $this->assertInstanceOf(\obray\ipp\types\StatusCode::class, $IPPPayload->statusCode);
        $this->assertInstanceOf(\obray\ipp\OperationAttributes::class, $IPPPayload->operationAttributes);
        $this->assertInstanceOf(\obray\ipp\PrinterDescriptionAttributes::class, $IPPPayload->printerDescriptionAttributes);

        // test if it was scuccessful
        $this->assertSame('successful-ok', (string)$IPPPayload->statusCode);
    }

    /**
     * Test Get Jobs
     */

    public function testGetJobs()
    {
        // do test printer call
        $IPPPayload = $this->printer->getJobs(375);
        
        // test if IPPPayload is properly structured
        $this->assertInstanceOf(\obray\ipp\transport\IPPPayload::class, $IPPPayload);
        $this->assertInstanceOf(\obray\ipp\types\VersionNumber::class, $IPPPayload->versionNumber);
        $this->assertInstanceOf(\obray\ipp\types\Integer::class, $IPPPayload->requestId);
        $this->assertSame(375, $IPPPayload->requestId->getValue());
        $this->assertInstanceOf(\obray\ipp\types\StatusCode::class, $IPPPayload->statusCode);
        $this->assertInstanceOf(\obray\ipp\OperationAttributes::class, $IPPPayload->operationAttributes);

        // test if it was scuccessful
        $this->assertSame('successful-ok', (string)$IPPPayload->statusCode);
        //print_r(json_encode($IPPPayload, JSON_PRETTY_PRINT));
    }

    /**
     * Test Pause Printer
     */

    public function testPausePrinter()
    {
        // todo
        $this->assertSame('blah', 'blah');
    }

    /**
     * Test Resume Printer
     */

    public function testResumePrinter()
    {
        // todo
        $this->assertSame('blah', 'blah');
    }

    /**
     * Test Purge Jobs
     */

    public function testPurgeJobs()
    {
        // todo
        $this->assertSame('blah', 'blah');
    }

}