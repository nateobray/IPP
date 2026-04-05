<?php
declare(strict_types=1);

$loader = require_once 'vendor/autoload.php';
require_once __DIR__ . '/RequestStub.php';
use PHPUnit\Framework\TestCase;

class PrinterOperationsTest extends TestCase
{
    public function testPrintUriBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->printURI('http://example.com/doc.pdf', 123, ['document-format' => 'application/pdf']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(1, $header['Major']);
        $this->assertSame(1, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::PRINT_URI, $header['Operation']);
        $this->assertSame(123, $header['RequestID']);
        $this->assertStringContainsString('http://example.com/doc.pdf', $binary);
    }

    public function testCreateJobBuildsOperation()
    {
        $printer = new \obray\ipp\Printer('ipp://localhost/printers/CUPS_PDF', 'user', 'pass', [], RequestStub::class);
        $printer->createJob(456, ['job-name' => 'Test Job']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(1, $header['Major']);
        $this->assertSame(1, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::CREATE_JOB, $header['Operation']);
        $this->assertSame(456, $header['RequestID']);
    }
}
