<?php
$loader = require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class IPPPayloadTest extends TestCase
{
    public function testDecodeSupportsMultipleJobAttributeGroupsInOneResponse(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'status-message'} = 'successful-ok';

        $jobAttributesA = new \obray\ipp\JobAttributes();
        $jobAttributesA->{'job-id'} = 101;
        $jobAttributesA->{'job-uri'} = 'ipp://printer.example/jobs/101';
        $jobAttributesA->{'job-name'} = 'First job';
        $jobAttributesA->{'job-state'} = \obray\ipp\enums\JobState::PENDING;

        $jobAttributesB = new \obray\ipp\JobAttributes();
        $jobAttributesB->{'job-id'} = 102;
        $jobAttributesB->{'job-uri'} = 'ipp://printer.example/jobs/102';
        $jobAttributesB->{'job-name'} = 'Second job';
        $jobAttributesB->{'job-state'} = \obray\ipp\enums\JobState::COMPLETED;

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 5001);
        $binary .= $operationAttributes->encode();
        $binary .= $jobAttributesA->encode();
        $binary .= $jobAttributesB->encode();
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertSame('1.1', (string) $payload->versionNumber);
        $this->assertSame(5001, $payload->requestId->getValue());
        $this->assertSame('successful-ok', (string) $payload->statusCode);
        $this->assertIsArray($payload->jobAttributes);
        $this->assertCount(2, $payload->jobAttributes);
        $this->assertSame('101', (string) $payload->jobAttributes[0]->{'job-id'});
        $this->assertSame('First job', (string) $payload->jobAttributes[0]->{'job-name'});
        $this->assertSame('pending', (string) $payload->jobAttributes[0]->{'job-state'});
        $this->assertSame('102', (string) $payload->jobAttributes[1]->{'job-id'});
        $this->assertSame('Second job', (string) $payload->jobAttributes[1]->{'job-name'});
        $this->assertSame('completed', (string) $payload->jobAttributes[1]->{'job-state'});
    }

    public function testDecodeSupportsPrinterAttributesAndUnsupportedAttributesInOneResponse(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'status-message'} = 'successful-ok-ignored-or-substituted-attributes';
        $operationAttributes->{'detailed-status-message'} = 'Ignored unsupported requested attributes';

        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'printer-name'} = 'Main printer';
        $printerAttributes->{'printer-state'} = \obray\ipp\enums\PrinterState::processing;
        $printerAttributes->{'charset-supported'} = ['utf-8', 'us-ascii'];
        $printerAttributes->{'document-format-supported'} = ['application/pdf', 'application/postscript'];
        $printerAttributes->{'operations-supported'} = [
            \obray\ipp\types\Operation::PRINT_JOB,
            \obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES,
        ];

        $unsupportedAttributes = new \obray\ipp\UnsupportedAttributes();
        $unsupportedAttributes->set(
            'requested-attributes',
            new \obray\ipp\Attribute('requested-attributes', 'printer-resolution-default', \obray\ipp\enums\Types::KEYWORD)
        );

        $binary = pack(
            'C2nN',
            1,
            1,
            \obray\ipp\types\StatusCode::successful_ok_ignored_or_substituted_attributes,
            5002
        );
        $binary .= $operationAttributes->encode();
        $binary .= $printerAttributes->encode();
        $binary .= $unsupportedAttributes->encode();
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertSame('1.1', (string) $payload->versionNumber);
        $this->assertSame(5002, $payload->requestId->getValue());
        $this->assertSame('successful-ok-ignored-or-substituted-attributes', (string) $payload->statusCode);
        $this->assertSame(
            'successful-ok-ignored-or-substituted-attributes',
            (string) $payload->operationAttributes->{'status-message'}
        );
        $this->assertSame(
            'Ignored unsupported requested attributes',
            (string) $payload->operationAttributes->{'detailed-status-message'}
        );
        $this->assertIsArray($payload->printerAttributes);
        $this->assertCount(1, $payload->printerAttributes);
        $this->assertSame('Main printer', (string) $payload->printerAttributes[0]->{'printer-name'});
        $this->assertSame('processing', (string) $payload->printerAttributes[0]->{'printer-state'});
        $this->assertIsArray($payload->printerAttributes[0]->{'charset-supported'});
        $this->assertSame('utf-8', (string) $payload->printerAttributes[0]->{'charset-supported'}[0]);
        $this->assertSame('us-ascii', (string) $payload->printerAttributes[0]->{'charset-supported'}[1]);
        $this->assertIsArray($payload->printerAttributes[0]->{'document-format-supported'});
        $this->assertSame('application/pdf', (string) $payload->printerAttributes[0]->{'document-format-supported'}[0]);
        $this->assertSame('application/postscript', (string) $payload->printerAttributes[0]->{'document-format-supported'}[1]);
        $this->assertIsArray($payload->printerAttributes[0]->{'operations-supported'});
        $this->assertSame('print-job', (string) $payload->printerAttributes[0]->{'operations-supported'}[0]);
        $this->assertSame(
            'get-printer-attributes',
            (string) $payload->printerAttributes[0]->{'operations-supported'}[1]
        );
        $this->assertIsArray($payload->unsupportedAttributes);
        $this->assertCount(1, $payload->unsupportedAttributes);
        $this->assertSame(
            'printer-resolution-default',
            (string) $payload->unsupportedAttributes[0]->{'requested-attributes'}
        );
    }

    public function testDecodeSupportsSingleJobAttributesResponseWithDetailedFields(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'status-message'} = 'successful-ok';

        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->{'job-id'} = 103;
        $jobAttributes->{'job-uri'} = 'ipp://printer.example/jobs/103';
        $jobAttributes->{'job-name'} = 'Detailed job';
        $jobAttributes->{'job-originating-user-name'} = 'alice';
        $jobAttributes->{'job-state'} = \obray\ipp\enums\JobState::PROCESSING;
        $jobAttributes->{'job-state-reasons'} = ['job-printing', 'job-interpreting'];
        $jobAttributes->{'job-detailed-status-messages'} = ['Rasterizing page 1', 'Waiting on media'];
        $jobAttributes->{'job-document-access-errors'} = ['fetch failed', 'timeout'];
        $jobAttributes->{'time-at-creation'} = 200;
        $jobAttributes->{'time-at-processing'} = 205;
        $jobAttributes->{'job-k-octets'} = 512;
        $jobAttributes->{'job-k-octets-processed'} = 256;

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 5003);
        $binary .= $operationAttributes->encode();
        $binary .= $jobAttributes->encode();
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertSame('1.1', (string) $payload->versionNumber);
        $this->assertSame(5003, $payload->requestId->getValue());
        $this->assertSame('successful-ok', (string) $payload->statusCode);
        $this->assertIsArray($payload->jobAttributes);
        $this->assertCount(1, $payload->jobAttributes);
        $this->assertSame('Detailed job', (string) $payload->jobAttributes[0]->{'job-name'});
        $this->assertSame('alice', (string) $payload->jobAttributes[0]->{'job-originating-user-name'});
        $this->assertSame('processing', (string) $payload->jobAttributes[0]->{'job-state'});
        $this->assertIsArray($payload->jobAttributes[0]->{'job-state-reasons'});
        $this->assertSame('job-printing', (string) $payload->jobAttributes[0]->{'job-state-reasons'}[0]);
        $this->assertSame('job-interpreting', (string) $payload->jobAttributes[0]->{'job-state-reasons'}[1]);
        $this->assertIsArray($payload->jobAttributes[0]->{'job-detailed-status-messages'});
        $this->assertSame(
            'Rasterizing page 1',
            (string) $payload->jobAttributes[0]->{'job-detailed-status-messages'}[0]
        );
        $this->assertSame(
            'Waiting on media',
            (string) $payload->jobAttributes[0]->{'job-detailed-status-messages'}[1]
        );
        $this->assertIsArray($payload->jobAttributes[0]->{'job-document-access-errors'});
        $this->assertSame(
            'fetch failed',
            (string) $payload->jobAttributes[0]->{'job-document-access-errors'}[0]
        );
        $this->assertSame(
            'timeout',
            (string) $payload->jobAttributes[0]->{'job-document-access-errors'}[1]
        );
        $this->assertSame('200', (string) $payload->jobAttributes[0]->{'time-at-creation'});
        $this->assertSame('205', (string) $payload->jobAttributes[0]->{'time-at-processing'});
        $this->assertSame('512', (string) $payload->jobAttributes[0]->{'job-k-octets'});
        $this->assertSame('256', (string) $payload->jobAttributes[0]->{'job-k-octets-processed'});
    }

    public function testDecodeAcceptsEmptyUnsupportedAttributesGroup(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'status-message'} = 'client-error-attributes-or-values-not-supported';

        $binary = pack(
            'C2nN',
            1,
            1,
            \obray\ipp\types\StatusCode::client_error_attributes_or_values_not_supported,
            5004
        );
        $binary .= $operationAttributes->encode();
        $binary .= pack('c', 0x05);
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertSame('1.1', (string) $payload->versionNumber);
        $this->assertSame(5004, $payload->requestId->getValue());
        $this->assertSame('client-error-attributes-or-values-not-supported', (string) $payload->statusCode);
        $this->assertIsArray($payload->unsupportedAttributes);
        $this->assertCount(1, $payload->unsupportedAttributes);
        $this->assertSame([], $payload->unsupportedAttributes[0]->jsonSerialize());
    }

    public function testDecodeSupportsConflictingAttributesResponseWithRepeatedUnsupportedAttributes(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'status-message'} = 'client-error-conflicting-attributes';
        $operationAttributes->{'detailed-status-message'} = 'Two requested attributes conflicted';

        $unsupportedAttributes = new \obray\ipp\UnsupportedAttributes();
        $unsupportedAttributes->set('requested-attributes', [
            new \obray\ipp\Attribute('requested-attributes', 'printer-resolution-default', \obray\ipp\enums\Types::KEYWORD),
            new \obray\ipp\Attribute('requested-attributes', 'media-ready', \obray\ipp\enums\Types::KEYWORD),
        ]);

        $binary = pack(
            'C2nN',
            1,
            1,
            \obray\ipp\types\StatusCode::client_error_conflicting_attributes,
            5005
        );
        $binary .= $operationAttributes->encode();
        $binary .= $unsupportedAttributes->encode();
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertSame('1.1', (string) $payload->versionNumber);
        $this->assertSame(5005, $payload->requestId->getValue());
        $this->assertSame('client-error-conflicting-attributes', (string) $payload->statusCode);
        $this->assertIsArray($payload->unsupportedAttributes);
        $this->assertCount(1, $payload->unsupportedAttributes);
        $this->assertIsArray($payload->unsupportedAttributes[0]->{'requested-attributes'});
        $this->assertSame(
            'printer-resolution-default',
            (string) $payload->unsupportedAttributes[0]->{'requested-attributes'}[0]
        );
        $this->assertSame(
            'media-ready',
            (string) $payload->unsupportedAttributes[0]->{'requested-attributes'}[1]
        );
    }

    public function testDecodeSupportsRichPrinterDescriptionResponse(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'status-message'} = 'successful-ok';

        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'printer-name'} = 'Main printer';
        $printerAttributes->{'printer-location'} = 'Level 2';
        $printerAttributes->{'printer-info'} = 'Front office device';
        $printerAttributes->{'printer-more-info'} = 'https://printer.example/info';
        $printerAttributes->{'printer-make-and-model'} = 'ExampleLaser 9000';
        $printerAttributes->{'printer-state'} = \obray\ipp\enums\PrinterState::processing;
        $printerAttributes->{'printer-state-reasons'} = ['none', 'moving-to-paused'];
        $printerAttributes->{'printer-state-message'} = 'Processing print queue';
        $printerAttributes->{'ipp-versions-supported'} = ['1.0', '1.1'];
        $printerAttributes->{'operations-supported'} = [
            \obray\ipp\types\Operation::PRINT_JOB,
            \obray\ipp\types\Operation::GET_JOBS,
            \obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES,
        ];
        $printerAttributes->{'multiple-document-jobs-supported'} = true;
        $printerAttributes->{'charset-configured'} = 'utf-8';
        $printerAttributes->{'charset-supported'} = ['utf-8', 'us-ascii'];
        $printerAttributes->{'natural-language-configured'} = 'en';
        $printerAttributes->{'generated-natural-language-supported'} = ['en', 'fr-ca'];
        $printerAttributes->{'document-format-default'} = 'application/pdf';
        $printerAttributes->{'document-format-supported'} = ['application/pdf', 'application/postscript'];
        $printerAttributes->{'printer-is-accepting-jobs'} = true;
        $printerAttributes->{'queued-job-count'} = 4;
        $printerAttributes->{'color-supported'} = true;
        $printerAttributes->{'reference-uri-schemes-supported'} = ['http', 'https'];
        $printerAttributes->{'pdl-override-supported'} = 'attempted';
        $printerAttributes->{'printer-up-time'} = 9000;
        $printerAttributes->{'printer-current-time'} = '2019-05-18 23:45:32.4-0700';
        $printerAttributes->{'multiple-operation-time-out'} = 60;
        $printerAttributes->{'compression-supported'} = ['none', 'gzip'];
        $printerAttributes->{'job-k-octets-supported'} = '0-9999';
        $printerAttributes->{'job-impressions-supported'} = '0-2000';
        $printerAttributes->{'job-media-sheets-supported'} = '0-1000';
        $printerAttributes->{'pages-per-minute'} = 35;
        $printerAttributes->{'pages-per-minute-color'} = 20;

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 5006);
        $binary .= $operationAttributes->encode();
        $binary .= $printerAttributes->encode();
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertSame('1.1', (string) $payload->versionNumber);
        $this->assertSame(5006, $payload->requestId->getValue());
        $this->assertSame('successful-ok', (string) $payload->statusCode);
        $this->assertIsArray($payload->printerAttributes);
        $this->assertCount(1, $payload->printerAttributes);
        $printer = $payload->printerAttributes[0];

        $this->assertSame('Main printer', (string) $printer->{'printer-name'});
        $this->assertSame('Level 2', (string) $printer->{'printer-location'});
        $this->assertSame('Front office device', (string) $printer->{'printer-info'});
        $this->assertSame('https://printer.example/info', (string) $printer->{'printer-more-info'});
        $this->assertSame('ExampleLaser 9000', (string) $printer->{'printer-make-and-model'});
        $this->assertSame('processing', (string) $printer->{'printer-state'});
        $this->assertIsArray($printer->{'printer-state-reasons'});
        $this->assertSame('none', (string) $printer->{'printer-state-reasons'}[0]);
        $this->assertSame('moving-to-paused', (string) $printer->{'printer-state-reasons'}[1]);
        $this->assertSame('Processing print queue', (string) $printer->{'printer-state-message'});
        $this->assertIsArray($printer->{'ipp-versions-supported'});
        $this->assertSame('1.0', (string) $printer->{'ipp-versions-supported'}[0]);
        $this->assertSame('1.1', (string) $printer->{'ipp-versions-supported'}[1]);
        $this->assertIsArray($printer->{'operations-supported'});
        $this->assertSame('print-job', (string) $printer->{'operations-supported'}[0]);
        $this->assertSame('get-jobs', (string) $printer->{'operations-supported'}[1]);
        $this->assertSame('get-printer-attributes', (string) $printer->{'operations-supported'}[2]);
        $this->assertSame('true', (string) $printer->{'multiple-document-jobs-supported'});
        $this->assertSame('utf-8', (string) $printer->{'charset-configured'});
        $this->assertIsArray($printer->{'charset-supported'});
        $this->assertSame('utf-8', (string) $printer->{'charset-supported'}[0]);
        $this->assertSame('us-ascii', (string) $printer->{'charset-supported'}[1]);
        $this->assertSame('en', (string) $printer->{'natural-language-configured'});
        $this->assertIsArray($printer->{'generated-natural-language-supported'});
        $this->assertSame('en', (string) $printer->{'generated-natural-language-supported'}[0]);
        $this->assertSame('fr-ca', (string) $printer->{'generated-natural-language-supported'}[1]);
        $this->assertSame('application/pdf', (string) $printer->{'document-format-default'});
        $this->assertIsArray($printer->{'document-format-supported'});
        $this->assertSame('application/pdf', (string) $printer->{'document-format-supported'}[0]);
        $this->assertSame('application/postscript', (string) $printer->{'document-format-supported'}[1]);
        $this->assertSame('true', (string) $printer->{'printer-is-accepting-jobs'});
        $this->assertSame('4', (string) $printer->{'queued-job-count'});
        $this->assertSame('true', (string) $printer->{'color-supported'});
        $this->assertIsArray($printer->{'reference-uri-schemes-supported'});
        $this->assertSame('http', (string) $printer->{'reference-uri-schemes-supported'}[0]);
        $this->assertSame('https', (string) $printer->{'reference-uri-schemes-supported'}[1]);
        $this->assertSame('attempted', (string) $printer->{'pdl-override-supported'});
        $this->assertSame('9000', (string) $printer->{'printer-up-time'});
        $this->assertSame('2019-05-18 23:45:32.400-0700', (string) $printer->{'printer-current-time'});
        $this->assertSame('60', (string) $printer->{'multiple-operation-time-out'});
        $this->assertIsArray($printer->{'compression-supported'});
        $this->assertSame('none', (string) $printer->{'compression-supported'}[0]);
        $this->assertSame('gzip', (string) $printer->{'compression-supported'}[1]);
        $this->assertSame('0-9999', (string) $printer->{'job-k-octets-supported'});
        $this->assertSame('0-2000', (string) $printer->{'job-impressions-supported'});
        $this->assertSame('0-1000', (string) $printer->{'job-media-sheets-supported'});
        $this->assertSame('35', (string) $printer->{'pages-per-minute'});
        $this->assertSame('20', (string) $printer->{'pages-per-minute-color'});
    }

    public function testDecodeSupportsRichCompletedJobResponse(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'status-message'} = 'successful-ok';

        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->{'job-id'} = 104;
        $jobAttributes->{'job-uri'} = 'ipp://printer.example/jobs/104';
        $jobAttributes->{'job-printer-uri'} = 'ipp://printer.example/printers/main';
        $jobAttributes->{'job-more-info'} = 'https://printer.example/jobs/104';
        $jobAttributes->{'job-name'} = 'Completed job';
        $jobAttributes->{'job-originating-user-name'} = 'bob';
        $jobAttributes->{'job-state'} = \obray\ipp\enums\JobState::COMPLETED;
        $jobAttributes->{'job-state-reasons'} = ['job-completed-successfully'];
        $jobAttributes->{'job-state-message'} = 'Completed successfully';
        $jobAttributes->{'job-message-from-operator'} = 'Collected from tray 1';
        $jobAttributes->{'number-of-documents'} = 1;
        $jobAttributes->{'output-device-assigned'} = 'output-bin-1';
        $jobAttributes->{'time-at-creation'} = 1000;
        $jobAttributes->{'time-at-processing'} = 1010;
        $jobAttributes->{'time-at-completed'} = 1020;
        $jobAttributes->{'job-printer-up-time'} = 12000;
        $jobAttributes->{'date-time-at-creation'} = '2019-05-18 23:45:32.4-0700';
        $jobAttributes->{'date-time-at-processing'} = '2019-05-18 23:46:32.4-0700';
        $jobAttributes->{'date-time-at-completed'} = '2019-05-18 23:47:32.4-0700';
        $jobAttributes->{'number-of-intervening-jobs'} = 0;
        $jobAttributes->{'job-k-octets'} = 1024;
        $jobAttributes->{'job-impressions'} = 12;
        $jobAttributes->{'job-media-sheets'} = 6;
        $jobAttributes->{'job-k-octets-processed'} = 1024;
        $jobAttributes->{'job-impressions-completed'} = 12;
        $jobAttributes->{'job-media-sheets-completed'} = 6;
        $jobAttributes->{'attributes-charset'} = 'utf-8';
        $jobAttributes->{'attributes-natural-language'} = 'en';

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 5007);
        $binary .= $operationAttributes->encode();
        $binary .= $jobAttributes->encode();
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertSame('1.1', (string) $payload->versionNumber);
        $this->assertSame(5007, $payload->requestId->getValue());
        $this->assertSame('successful-ok', (string) $payload->statusCode);
        $this->assertIsArray($payload->jobAttributes);
        $this->assertCount(1, $payload->jobAttributes);
        $job = $payload->jobAttributes[0];

        $this->assertSame('104', (string) $job->{'job-id'});
        $this->assertSame('ipp://printer.example/jobs/104', (string) $job->{'job-uri'});
        $this->assertSame('ipp://printer.example/printers/main', (string) $job->{'job-printer-uri'});
        $this->assertSame('https://printer.example/jobs/104', (string) $job->{'job-more-info'});
        $this->assertSame('Completed job', (string) $job->{'job-name'});
        $this->assertSame('bob', (string) $job->{'job-originating-user-name'});
        $this->assertSame('completed', (string) $job->{'job-state'});
        $this->assertSame('job-completed-successfully', (string) $job->{'job-state-reasons'});
        $this->assertSame('Completed successfully', (string) $job->{'job-state-message'});
        $this->assertSame('Collected from tray 1', (string) $job->{'job-message-from-operator'});
        $this->assertSame('1', (string) $job->{'number-of-documents'});
        $this->assertSame('output-bin-1', (string) $job->{'output-device-assigned'});
        $this->assertSame('1000', (string) $job->{'time-at-creation'});
        $this->assertSame('1010', (string) $job->{'time-at-processing'});
        $this->assertSame('1020', (string) $job->{'time-at-completed'});
        $this->assertSame('12000', (string) $job->{'job-printer-up-time'});
        $this->assertSame('2019-05-18 23:45:32.400-0700', (string) $job->{'date-time-at-creation'});
        $this->assertSame('2019-05-18 23:46:32.400-0700', (string) $job->{'date-time-at-processing'});
        $this->assertSame('2019-05-18 23:47:32.400-0700', (string) $job->{'date-time-at-completed'});
        $this->assertSame('0', (string) $job->{'number-of-intervening-jobs'});
        $this->assertSame('1024', (string) $job->{'job-k-octets'});
        $this->assertSame('12', (string) $job->{'job-impressions'});
        $this->assertSame('6', (string) $job->{'job-media-sheets'});
        $this->assertSame('1024', (string) $job->{'job-k-octets-processed'});
        $this->assertSame('12', (string) $job->{'job-impressions-completed'});
        $this->assertSame('6', (string) $job->{'job-media-sheets-completed'});
        $this->assertSame('utf-8', (string) $job->{'attributes-charset'});
        $this->assertSame('en', (string) $job->{'attributes-natural-language'});
    }

    public function testDecodeRejectsTruncatedHeader(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('IPP header');

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode("\x01\x01\x00");
    }

    public function testDecodeRejectsMissingOperationAttributesTag(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected operation-attributes-tag');

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode(pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 6001) . pack('c', 0x04) . pack('c', 0x03));
    }

    public function testDecodeRejectsTruncatedAttributeValue(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Truncated IPP payload while decoding US-ASCII string');

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 6002);
        $binary .= pack('c', 0x01);
        $binary .= pack('c', \obray\ipp\enums\Types::KEYWORD);
        $binary .= pack('n', 4) . 'note';
        $binary .= pack('n', 4) . 'ok';

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);
    }

    public function testDecodeRejectsMissingEndOfAttributesTag(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Missing end-of-attributes tag');

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode(pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 6003) . (new \obray\ipp\OperationAttributes())->encode());
    }

    public function testDecodeRejectsUnexpectedAttributeGroupTag(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unexpected attribute group tag');

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 6004);
        $binary .= (new \obray\ipp\OperationAttributes())->encode();
        $binary .= pack('c', 0x07);
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);
    }

    public function testDecodePreservesUnknownValueTagsAsUnknownTypes(): void
    {
        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 6005);
        $binary .= pack('c', 0x01);
        $binary .= pack('c', 0x7f);
        $binary .= pack('n', 6) . 'x-test';
        $binary .= pack('n', 0);
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertTrue($payload->operationAttributes->has('x-test'));
        $this->assertInstanceOf(
            \obray\ipp\types\Unknown::class,
            $payload->operationAttributes->{'x-test'}->getAttributeValueClass()
        );
    }

    public function testDecodePreservesNoValueOutOfBandAttributes(): void
    {
        $attributeName = 'printer-state-message';

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 6006);
        $binary .= (new \obray\ipp\OperationAttributes())->encode();
        $binary .= pack('c', 0x04);
        $binary .= pack('c', \obray\ipp\enums\Types::NOVAL);
        $binary .= pack('n', strlen($attributeName)) . $attributeName;
        $binary .= pack('n', 0);
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);

        $this->assertIsArray($payload->printerAttributes);
        $this->assertCount(1, $payload->printerAttributes);
        $this->assertTrue($payload->printerAttributes[0]->has('printer-state-message'));
        $this->assertInstanceOf(
            \obray\ipp\types\NoVal::class,
            $payload->printerAttributes[0]->{'printer-state-message'}->getAttributeValueClass()
        );
    }

    public function testDecodeRejectsOmittedNameBeforeAnyAttributeNameWasSeen(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('omitted name before any attribute name was decoded');

        $binary = pack('C2nN', 1, 1, \obray\ipp\types\StatusCode::successful_ok, 6007);
        $binary .= pack('c', 0x01);
        $binary .= pack('c', \obray\ipp\enums\Types::KEYWORD);
        $binary .= pack('n', 0);
        $binary .= pack('n', 4) . 'test';
        $binary .= pack('c', 0x03);

        $payload = new \obray\ipp\transport\IPPPayload();
        $payload->decode($binary);
    }
}
