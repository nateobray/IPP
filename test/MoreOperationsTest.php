<?php
declare(strict_types=1);

$loader = require_once 'vendor/autoload.php';
require_once __DIR__ . '/RequestStub.php';

use PHPUnit\Framework\TestCase;

class MoreOperationsTest extends TestCase
{
    public function testGetPrinterSupportedValuesBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->getPrinterSupportedValues(111, ['requested-attributes' => 'media']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(2, $header['Major']);
        $this->assertSame(0, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::GET_PRINTER_SUPPORTED_VALUES, $header['Operation']);
        $this->assertSame(111, $header['RequestID']);
    }

    public function testGetResourceAttributesBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->getResourceAttributes(112, ['resource-name' => 'example']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(2, $header['Major']);
        $this->assertSame(0, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::GET_RESOURCE_ATTRIBUTES, $header['Operation']);
        $this->assertSame(112, $header['RequestID']);
    }

    public function testGetResourceDataBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->getResourceData(113, ['resource-name' => 'example', 'resource-format' => 'application/pdf']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(2, $header['Major']);
        $this->assertSame(0, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::GET_RESOURCE_DATA, $header['Operation']);
        $this->assertSame(113, $header['RequestID']);
    }

    public function testGetResourcesBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->getResources(114, ['resource-type' => 'resource']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(2, $header['Major']);
        $this->assertSame(0, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::GET_RESOURCES, $header['Operation']);
        $this->assertSame(114, $header['RequestID']);
    }

    public function testCancelJobsBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->cancelJobs(222);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(2, $header['Major']);
        $this->assertSame(0, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::CANCEL_JOBS, $header['Operation']);
        $this->assertSame(222, $header['RequestID']);
    }

    public function testCancelMyJobsBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->cancelMyJobs(333);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(2, $header['Major']);
        $this->assertSame(0, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::CANCEL_MY_JOBS, $header['Operation']);
        $this->assertSame(333, $header['RequestID']);
    }

    public function testCloseJobBuildsOperation()
    {
        $job = new \obray\ipp\Job('ipp://localhost/printers/CUPS_PDF', 123, 'user', 'pass', [], RequestStub::class);
        $job->closeJob(444);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(2, $header['Major']);
        $this->assertSame(0, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::CLOSE_JOB, $header['Operation']);
        $this->assertSame(444, $header['RequestID']);
    }
}
