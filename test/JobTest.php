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

    public function testSendDocumentRejectsEmptyNonFinalPayload(): void
    {
        try {
            $this->job->sendDocument('', false, 112);
            $this->fail('Expected Send-Document validation to reject an empty non-final payload.');
        } catch (\obray\ipp\exceptions\InvalidRequest $exception) {
            $this->assertSame([], FakeRequest::$lastCall);
            $this->assertStringContainsString('Send-Document without document data requires "last-document" to be true', $exception->getMessage());
        }
    }

    public function testJobUriTargetFormUsesOnlyJobUriOperationAttribute(): void
    {
        $job = (new \obray\ipp\Job(
            'ipp://localhost/printers/CUPS_PDF',
            'ipp://localhost/jobs/42',
            'demo-user',
            'secret'
        ))->setRequestClass(FakeRequest::class);

        $job->getJobAttributes(113, ['job-id']);

        $this->assertSame('ipp://localhost/jobs/42', FakeRequest::$lastCall['printerURI']);
        $this->assertTrue(FakeRequest::$lastCall['operationAttributes']->has('job-uri'));
        $this->assertFalse(FakeRequest::$lastCall['operationAttributes']->has('printer-uri'));
        $this->assertSame('ipp://localhost/jobs/42', (string) FakeRequest::$lastCall['operationAttributes']->{'job-uri'});
    }

    public function testSetJobAttributesBuildsExpectedPayload(): void
    {
        $this->job->setJobAttributes(['job-priority' => 50], 114);

        $this->assertSame(\obray\ipp\types\Operation::SET_JOB_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame(114, FakeRequest::$lastCall['requestId']);
        $this->assertSame('1.1', FakeRequest::$lastCall['version']);
        $this->assertNotNull(FakeRequest::$lastCall['jobAttributes']);
        $this->assertSame('50', (string) FakeRequest::$lastCall['jobAttributes']->{'job-priority'});
    }

    public function testCancelCurrentJobBuildsExpectedPayload(): void
    {
        $this->job->cancelCurrentJob(118);
        $this->assertSame(\obray\ipp\types\Operation::CANCEL_CURRENT_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(118, FakeRequest::$lastCall['requestId']);
    }

    public function testSuspendCurrentJobBuildsExpectedPayload(): void
    {
        $this->job->suspendCurrentJob(119);
        $this->assertSame(\obray\ipp\types\Operation::SUSPEND_CURRENT_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(119, FakeRequest::$lastCall['requestId']);
    }

    public function testResumeJobBuildsExpectedPayload(): void
    {
        $this->job->resumeJob(120);
        $this->assertSame(\obray\ipp\types\Operation::RESUME_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(120, FakeRequest::$lastCall['requestId']);
    }

    public function testPromoteJobBuildsExpectedPayload(): void
    {
        $this->job->promoteJob(121);
        $this->assertSame(\obray\ipp\types\Operation::PROMOTE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(121, FakeRequest::$lastCall['requestId']);
    }

    public function testReprocessJobBuildsExpectedPayload(): void
    {
        $this->job->reprocessJob(122);
        $this->assertSame(\obray\ipp\types\Operation::REPROCESS_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(122, FakeRequest::$lastCall['requestId']);
    }

    public function testScheduleJobAfterWithJobIdBuildsExpectedPayload(): void
    {
        $this->job->scheduleJobAfter(99, 123);
        $this->assertSame(\obray\ipp\types\Operation::SCHEDULE_JOB_AFTER, FakeRequest::$lastCall['operation']);
        $this->assertSame(123, FakeRequest::$lastCall['requestId']);
        $this->assertSame('99', (string) FakeRequest::$lastCall['operationAttributes']->{'job-id-after'});
    }

    public function testScheduleJobAfterWithJobUriBuildsExpectedPayload(): void
    {
        $this->job->scheduleJobAfter('ipp://localhost/jobs/99', 124);
        $this->assertSame(\obray\ipp\types\Operation::SCHEDULE_JOB_AFTER, FakeRequest::$lastCall['operation']);
        $this->assertSame('ipp://localhost/jobs/99', (string) FakeRequest::$lastCall['operationAttributes']->{'job-uri-after'});
    }

    public function testCloseJobBuildsExpectedPayload(): void
    {
        $this->job->closeJob(115);

        $this->assertSame(\obray\ipp\types\Operation::CLOSE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(115, FakeRequest::$lastCall['requestId']);
        $this->assertSame('2.0', FakeRequest::$lastCall['version']);
    }

    public function testMoveJobBuildsExpectedPayload(): void
    {
        $this->job->moveJob('ipp://localhost/printers/OTHER', 116);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_MOVE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(116, FakeRequest::$lastCall['requestId']);
        $this->assertSame('ipp://localhost/printers/OTHER', (string) FakeRequest::$lastCall['operationAttributes']->{'job-printer-uri'});
    }

    public function testAuthenticateJobBuildsExpectedPayload(): void
    {
        $this->job->authenticateJob(117);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_AUTHENTICATE_JOB, FakeRequest::$lastCall['operation']);
        $this->assertSame(117, FakeRequest::$lastCall['requestId']);
    }
}
