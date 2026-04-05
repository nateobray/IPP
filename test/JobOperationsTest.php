<?php
declare(strict_types=1);

$loader = require_once 'vendor/autoload.php';
require_once __DIR__ . '/RequestStub.php';
use PHPUnit\Framework\TestCase;

class JobOperationsTest extends TestCase
{
    public function testSendDocumentBuildsOperation()
    {
        $job = new \obray\ipp\Job('ipp://localhost/printers/CUPS_PDF', 99, 'user', 'pass', [], RequestStub::class);
        $job->sendDocument('Hello world', 789, true, ['document-format' => 'text/plain']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(1, $header['Major']);
        $this->assertSame(1, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::SEND_DOCUMENT, $header['Operation']);
        $this->assertSame(789, $header['RequestID']);
        $this->assertStringContainsString('Hello world', $binary);
    }

    public function testSendUriBuildsOperation()
    {
        $job = new \obray\ipp\Job('ipp://localhost/printers/CUPS_PDF', 101, 'user', 'pass', [], RequestStub::class);
        $job->sendURI('http://example.com/doc.pdf', 321, ['document-format' => 'application/pdf']);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(1, $header['Major']);
        $this->assertSame(1, $header['Minor']);
        $this->assertSame(\obray\ipp\types\Operation::SEND_URI, $header['Operation']);
        $this->assertSame(321, $header['RequestID']);
        $this->assertStringContainsString('http://example.com/doc.pdf', $binary);
    }

    public function testJobUriTargetIsEncoded()
    {
        $job = new \obray\ipp\Job('ipp://localhost/printers/CUPS_PDF', 'ipp://localhost/jobs/123', 'user', 'pass', [], RequestStub::class);
        $job->cancelJob(555);

        $binary = RequestStub::$last['encodedPayload'];
        $header = unpack('cMajor/cMinor/nOperation/NRequestID', $binary);

        $this->assertSame(\obray\ipp\types\Operation::CANCEL_JOB, $header['Operation']);
        $this->assertSame(555, $header['RequestID']);
        $this->assertStringContainsString('ipp://localhost/jobs/123', $binary);
    }
}
