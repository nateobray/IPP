<?php
$loader = require_once 'vendor/autoload.php';

use obray\ipp\test\FakeRequest;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    protected \obray\ipp\Job $job;

    protected function setUp(): void
    {
        FakeRequest::reset();

        $this->job = (new \obray\ipp\Job(
            'ipp://localhost/printers/CUPS_PDF',
            42,
            'demo-user',
            'secret'
        ))->setRequestClass(FakeRequest::class);
    }

    public function testSendDocumentBuildsExpectedPayload(): void
    {
        $this->job->sendDocument('Document body', false, 101, [
            'document-format' => 'application/pdf',
        ]);

        $this->assertSame(\obray\ipp\types\Operation::SEND_DOCUMENT, FakeRequest::$lastCall['operation']);
        $this->assertSame(101, FakeRequest::$lastCall['requestId']);
        $this->assertSame('application/pdf', (string) FakeRequest::$lastCall['operationAttributes']->{'document-format'});
        $this->assertSame('false', (string) FakeRequest::$lastCall['operationAttributes']->{'last-document'});
        $this->assertSame('Document body', FakeRequest::$lastCall['document']);
    }

    public function testSendDocumentAllowsEmptyFinalPayload(): void
    {
        $this->job->sendDocument('', true, 102);

        $this->assertSame(\obray\ipp\types\Operation::SEND_DOCUMENT, FakeRequest::$lastCall['operation']);
        $this->assertSame('true', (string) FakeRequest::$lastCall['operationAttributes']->{'last-document'});
        $this->assertSame('', FakeRequest::$lastCall['document']);
    }

    public function testSendURIBuildsExpectedPayload(): void
    {
        $this->job->sendURI('https://example.test/file.pdf', true, 103);

        $this->assertSame(\obray\ipp\types\Operation::SEND_URI, FakeRequest::$lastCall['operation']);
        $this->assertSame('https://example.test/file.pdf', (string) FakeRequest::$lastCall['operationAttributes']->{'document-uri'});
        $this->assertSame('true', (string) FakeRequest::$lastCall['operationAttributes']->{'last-document'});
    }

    public function testCancelJobBuildsExpectedPayload(): void
    {
        $this->job->cancelJob(104);

        $this->assertSame(\obray\ipp\types\Operation::CANCEL_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('42', (string) FakeRequest::$lastCall['operationAttributes']->{'job-id'});
    }

    public function testGetJobAttributesBuildsExpectedPayload(): void
    {
        $this->job->getJobAttributes(105);

        $this->assertSame(\obray\ipp\types\Operation::GET_JOB_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame(105, FakeRequest::$lastCall['requestId']);
    }

    public function testGetJobAttributesSupportsRequestedAttributes(): void
    {
        $this->job->getJobAttributes(109, ['job-id', 'job-state']);

        $this->assertIsArray(FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'});
        $this->assertSame('job-id', (string) FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'}[0]);
        $this->assertSame('job-state', (string) FakeRequest::$lastCall['operationAttributes']->{'requested-attributes'}[1]);
    }

    public function testHoldJobBuildsExpectedPayload(): void
    {
        $this->job->holdJob(106);

        $this->assertSame(\obray\ipp\types\Operation::HOLD_JOB, FakeRequest::$lastCall['operation']);
    }

    public function testHoldJobSupportsJobHoldUntil(): void
    {
        $this->job->holdJob(110, 'night');

        $this->assertSame(\obray\ipp\types\Operation::HOLD_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('night', (string) FakeRequest::$lastCall['operationAttributes']->{'job-hold-until'});
    }

    public function testReleaseJobBuildsExpectedPayload(): void
    {
        $this->job->releaseJob(107);

        $this->assertSame(\obray\ipp\types\Operation::RELEASE_JOB, FakeRequest::$lastCall['operation']);
    }

    public function testRestartJobBuildsExpectedPayload(): void
    {
        $this->job->restartJob(108);

        $this->assertSame(\obray\ipp\types\Operation::RESTART_JOB, FakeRequest::$lastCall['operation']);
    }

    public function testRestartJobSupportsJobHoldUntil(): void
    {
        $this->job->restartJob(111, 'indefinite');

        $this->assertSame(\obray\ipp\types\Operation::RESTART_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame('indefinite', (string) FakeRequest::$lastCall['operationAttributes']->{'job-hold-until'});
    }
}
