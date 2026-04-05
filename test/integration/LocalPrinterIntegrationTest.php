<?php
declare(strict_types=1);

$loader = require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once __DIR__ . '/LocalPrinterTestSupport.php';

use PHPUnit\Framework\TestCase;

final class LocalPrinterIntegrationTest extends TestCase
{
    private static ?LocalPrinterTarget $target = null;
    private static ?LocalPrinterTarget $jobTarget = null;
    private static array $preferredDocumentFormats = [
        'text/plain',
        'application/octet-stream',
        'application/pdf',
        'image/pwg-raster',
        'image/urf',
    ];

    public static function setUpBeforeClass(): void
    {
        self::$target = LocalPrinterTarget::discover();
        self::$jobTarget = LocalPrinterTarget::discover(true);
    }

    public function testCanDiscoverARealPrinterTarget(): void
    {
        $this->assertNotNull(
            self::$target,
            'No reachable CUPS/local printer target was discovered. Set IPP_TEST_URI or IPP_TEST_QUEUE to point the suite at a printer.'
        );
    }

    public function testGetPrinterAttributesAgainstRealPrinter(): void
    {
        $target = $this->requireTarget();
        $response = $target->printer()->getPrinterAttributes(101, ['printer-name', 'operations-supported', 'document-format-supported']);

        $this->assertSame('successful-ok', (string) $response->statusCode);
        $this->assertSame(101, $response->requestId->getValue());

        $printerAttributes = LocalPrinterTarget::firstAttributeGroup($response->printerAttributes);
        $this->assertNotNull($printerAttributes);
        $this->assertTrue($printerAttributes->has('printer-name'));
        $this->assertTrue($printerAttributes->has('operations-supported'));
    }

    public function testValidateJobAgainstRealPrinter(): void
    {
        $target = $this->requireTarget();
        $documentFormat = $this->preferredDocumentFormat($target);
        $response = $target->printer()->validateJob(102, [
            'document-format' => $documentFormat,
            'job-hold-until' => 'indefinite',
        ]);

        $this->assertSuccessfulStatus($response);
        $this->assertSame(102, $response->requestId->getValue());
    }

    public function testGetJobsAgainstRealPrinter(): void
    {
        $target = $this->requireTarget();
        $response = $target->printer()->getJobs(103, 'not-completed', 5, false, ['job-id', 'job-name', 'job-state']);

        $this->assertSame('successful-ok', (string) $response->statusCode);
        $this->assertSame(103, $response->requestId->getValue());
        $this->assertIsArray($response->jobAttributes ?? []);
    }

    public function testCreateSendInspectAndCancelHeldJobAgainstRealPrinter(): void
    {
        $target = $this->requireJobTarget();
        $job = null;

        try {
            $createResponse = $target->printer()->createJob(104, [
                'job-name' => 'IPP Integration ' . date('YmdHis'),
                'job-hold-until' => 'indefinite',
            ]);
        } catch (\obray\ipp\exceptions\AuthenticationError $exception) {
            $this->markTestSkipped('Create-Job requires authentication on this queue. Set IPP_TEST_USER/IPP_TEST_PASSWORD or target a local CUPS queue that accepts the current user.');
        }

        $this->assertSuccessfulStatus($createResponse);

        $jobAttributes = LocalPrinterTarget::firstAttributeGroup($createResponse->jobAttributes);
        if ($jobAttributes === null || !$jobAttributes->has('job-id')) {
            $this->markTestSkipped('Create-Job succeeded but did not return a job-id. This target does not provide a usable continuation point for Send-Document in the current safe test flow.');
        }

        $jobId = (int) $jobAttributes->{'job-id'}->getAttributeValue();
        $job = $target->job($jobId);
        $documentFormat = $this->preferredDocumentFormat($target);

        try {
            $sendResponse = $job->sendDocument("IPP integration probe\n", true, 105, [
                'document-format' => $documentFormat,
            ]);
        } catch (\obray\ipp\exceptions\AuthenticationError $exception) {
            $this->markTestSkipped('Send-Document requires authentication on this queue. Set IPP_TEST_USER/IPP_TEST_PASSWORD or target a queue managed by the current user.');
        } finally {
            if ($job instanceof \obray\ipp\Job) {
                try {
                    $job->cancelJob(107);
                } catch (\Throwable) {
                }
            }
        }

        $this->assertSuccessfulStatus($sendResponse);

        $inspectResponse = $job->getJobAttributes(106, ['job-id', 'job-state', 'job-state-reasons']);
        $this->assertSuccessfulStatus($inspectResponse);

        $inspectedJobAttributes = LocalPrinterTarget::firstAttributeGroup($inspectResponse->jobAttributes);
        $this->assertNotNull($inspectedJobAttributes);
        $this->assertSame((string) $jobId, (string) $inspectedJobAttributes->{'job-id'});
    }

    private function requireTarget(): LocalPrinterTarget
    {
        if (self::$target === null) {
            $this->markTestSkipped('No reachable CUPS/local printer target was discovered.');
        }

        return self::$target;
    }

    private function requireJobTarget(): LocalPrinterTarget
    {
        if (self::$jobTarget === null) {
            $this->markTestSkipped('No discovered printer advertises create-job/send-document/cancel-job through the local CUPS instance.');
        }

        return self::$jobTarget;
    }

    private function preferredDocumentFormat(LocalPrinterTarget $target): string
    {
        $response = $target->printer()->getPrinterAttributes(150, ['document-format-supported']);
        $printerAttributes = LocalPrinterTarget::firstAttributeGroup($response->printerAttributes);

        if ($printerAttributes === null || !$printerAttributes->has('document-format-supported')) {
            return 'application/octet-stream';
        }

        $supportedFormats = LocalPrinterTarget::attributeValues($printerAttributes->{'document-format-supported'});
        foreach (self::$preferredDocumentFormats as $preferredFormat) {
            if (in_array($preferredFormat, $supportedFormats, true)) {
                return $preferredFormat;
            }
        }

        return $supportedFormats[0] ?? 'application/octet-stream';
    }

    private function assertSuccessfulStatus(\obray\ipp\transport\IPPPayload $response): void
    {
        $status = (string) $response->statusCode;

        $this->assertTrue(
            str_starts_with($status, 'successful-ok'),
            sprintf('Expected a successful IPP status, got "%s".', $status)
        );
    }
}
