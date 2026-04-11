<?php
$loader = require_once 'vendor/autoload.php';

use obray\ipp\test\FakeRequest;
use PHPUnit\Framework\TestCase;

class CupsJobTest extends TestCase
{
    protected \obray\ipp\Job $job;

    protected function setUp(): void
    {
        FakeRequest::reset();

        $this->job = (new \obray\ipp\Job(
            'ipp://localhost/printers/PDF',
            42,
            'admin',
            'secret'
        ))->setRequestClass(FakeRequest::class);
    }

    public function testMoveJobSendsCorrectOperation(): void
    {
        $this->job->moveJob('ipp://localhost/printers/Laser', 1);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_MOVE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('ipp://localhost/printers/PDF', FakeRequest::$lastCall['printerURI']);
        $this->assertSame('42', (string) FakeRequest::$lastCall['operationAttributes']->{'job-id'});
    }

    public function testMoveJobSetsDestinationPrinterURI(): void
    {
        $this->job->moveJob('ipp://localhost/printers/Laser', 7);

        $this->assertSame(
            'ipp://localhost/printers/Laser',
            (string) FakeRequest::$lastCall['operationAttributes']->{'job-printer-uri'}
        );
        $this->assertSame(7, FakeRequest::$lastCall['requestId']);
    }

    public function testAuthenticateJobSendsCorrectOperation(): void
    {
        $this->job->authenticateJob(3);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_AUTHENTICATE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('ipp://localhost/printers/PDF', FakeRequest::$lastCall['printerURI']);
        $this->assertSame('42', (string) FakeRequest::$lastCall['operationAttributes']->{'job-id'});
        $this->assertSame(3, FakeRequest::$lastCall['requestId']);
    }
}
