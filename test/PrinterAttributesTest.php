<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class PrinterAttributesTest extends TestCase
{
    public function testPrinterAttributesExposeCommonIpp11PrinterFields(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'charset-configured'} = 'utf-8';
        $printerAttributes->{'charset-supported'} = ['utf-8', 'us-ascii'];
        $printerAttributes->{'document-format-default'} = 'application/pdf';
        $printerAttributes->{'document-format-supported'} = ['application/pdf', 'application/postscript'];
        $printerAttributes->{'generated-natural-language-supported'} = ['en', 'fr-ca'];
        $printerAttributes->{'job-k-octets-supported'} = '0-999';
        $printerAttributes->{'job-impressions-supported'} = '0-500';
        $printerAttributes->{'job-media-sheets-supported'} = '0-250';
        $printerAttributes->{'multiple-operation-time-out'} = 60;
        $printerAttributes->{'operations-supported'} = [
            \obray\ipp\types\Operation::PRINT_JOB,
            \obray\ipp\types\Operation::GET_JOBS,
        ];
        $printerAttributes->{'pages-per-minute'} = 30;
        $printerAttributes->{'pages-per-minute-color'} = 20;
        $printerAttributes->{'printer-current-time'} = '2019-05-18 23:45:32.4-0700';
        $printerAttributes->{'printer-driver-installer'} = 'https://printer.example/driver';
        $printerAttributes->{'printer-name'} = 'Main printer';
        $printerAttributes->{'printer-info'} = 'Front office';
        $printerAttributes->{'printer-location'} = 'Level 2';
        $printerAttributes->{'printer-state'} = \obray\ipp\enums\PrinterState::processing;
        $printerAttributes->{'printer-state-message'} = 'Processing job';
        $printerAttributes->{'printer-more-info-manufacturer'} = 'https://printer.example/support';
        $printerAttributes->{'printer-state-reasons'} = ['none', 'moving-to-paused'];
        $printerAttributes->{'printer-uri-supported'} = ['ipp://printer.example/printers/main'];
        $printerAttributes->{'uri-authentication-supported'} = ['none', 'requesting-user-name'];
        $printerAttributes->{'uri-security-supported'} = ['none'];
        $printerAttributes->{'queued-job-count'} = 3;

        $this->assertSame('utf-8', (string) $printerAttributes->{'charset-configured'});
        $this->assertIsArray($printerAttributes->{'charset-supported'});
        $this->assertSame('utf-8', (string) $printerAttributes->{'charset-supported'}[0]);
        $this->assertSame('us-ascii', (string) $printerAttributes->{'charset-supported'}[1]);
        $this->assertSame('application/pdf', (string) $printerAttributes->{'document-format-default'});
        $this->assertIsArray($printerAttributes->{'generated-natural-language-supported'});
        $this->assertSame('en', (string) $printerAttributes->{'generated-natural-language-supported'}[0]);
        $this->assertSame('fr-ca', (string) $printerAttributes->{'generated-natural-language-supported'}[1]);
        $this->assertSame('0-999', (string) $printerAttributes->{'job-k-octets-supported'});
        $this->assertSame('0-500', (string) $printerAttributes->{'job-impressions-supported'});
        $this->assertSame('0-250', (string) $printerAttributes->{'job-media-sheets-supported'});
        $this->assertSame('60', (string) $printerAttributes->{'multiple-operation-time-out'});
        $this->assertSame('30', (string) $printerAttributes->{'pages-per-minute'});
        $this->assertSame('20', (string) $printerAttributes->{'pages-per-minute-color'});
        $this->assertSame('2019-05-18 23:45:32.400-0700', (string) $printerAttributes->{'printer-current-time'});
        $this->assertSame('https://printer.example/driver', (string) $printerAttributes->{'printer-driver-installer'});
        $this->assertSame('Main printer', (string) $printerAttributes->{'printer-name'});
        $this->assertSame('Front office', (string) $printerAttributes->{'printer-info'});
        $this->assertSame('Level 2', (string) $printerAttributes->{'printer-location'});
        $this->assertSame('processing', (string) $printerAttributes->{'printer-state'});
        $this->assertSame('Processing job', (string) $printerAttributes->{'printer-state-message'});
        $this->assertSame('https://printer.example/support', (string) $printerAttributes->{'printer-more-info-manufacturer'});
        $this->assertIsArray($printerAttributes->{'operations-supported'});
        $this->assertSame('print-job', (string) $printerAttributes->{'operations-supported'}[0]);
        $this->assertSame('get-jobs', (string) $printerAttributes->{'operations-supported'}[1]);
        $this->assertIsArray($printerAttributes->{'printer-state-reasons'});
        $this->assertSame('none', (string) $printerAttributes->{'printer-state-reasons'}[0]);
        $this->assertSame('moving-to-paused', (string) $printerAttributes->{'printer-state-reasons'}[1]);
        $this->assertSame('3', (string) $printerAttributes->{'queued-job-count'});
    }

    public function testPrinterStateMessageRetainsItsAttributeNameWhenDecoded(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'printer-state-message'} = 'Paused for maintenance';

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertTrue($decoded->has('printer-state-message'));
        $this->assertSame('Paused for maintenance', (string) $decoded->{'printer-state-message'});
    }

    public function testMultiValuePrinterAttributesDecodeToArrays(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'document-format-supported'} = ['application/pdf', 'image/pwg-raster'];
        $printerAttributes->{'generated-natural-language-supported'} = ['en', 'de'];
        $printerAttributes->{'uri-authentication-supported'} = ['none', 'basic'];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'document-format-supported'});
        $this->assertSame('application/pdf', (string) $decoded->{'document-format-supported'}[0]);
        $this->assertSame('image/pwg-raster', (string) $decoded->{'document-format-supported'}[1]);
        $this->assertIsArray($decoded->{'generated-natural-language-supported'});
        $this->assertSame('en', (string) $decoded->{'generated-natural-language-supported'}[0]);
        $this->assertSame('de', (string) $decoded->{'generated-natural-language-supported'}[1]);
        $this->assertIsArray($decoded->{'uri-authentication-supported'});
        $this->assertSame('none', (string) $decoded->{'uri-authentication-supported'}[0]);
        $this->assertSame('basic', (string) $decoded->{'uri-authentication-supported'}[1]);
    }

    // PWG5100.2 — output-bin

    public function testOutputBinDefaultEncodesAsKeyword(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'output-bin-default'} = 'face-down';

        $this->assertSame('face-down', (string) $printerAttributes->{'output-bin-default'});
        $this->assertInstanceOf(
            \obray\ipp\types\Keyword::class,
            $printerAttributes->{'output-bin-default'}->getAttributeValueClass()
        );
    }

    public function testOutputBinSupportedEncodes1setOfKeywords(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'output-bin-supported'} = ['face-up', 'face-down', 'top'];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'output-bin-supported'});
        $this->assertSame('face-up',   (string) $decoded->{'output-bin-supported'}[0]);
        $this->assertSame('face-down', (string) $decoded->{'output-bin-supported'}[1]);
        $this->assertSame('top',       (string) $decoded->{'output-bin-supported'}[2]);
    }

    // PWG5101.1 — media description attributes

    public function testMediaDefaultEncodesAsKeyword(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'media-default'} = \obray\ipp\enums\MediaSize::ISO_A4;

        $this->assertSame(\obray\ipp\enums\MediaSize::ISO_A4, (string) $printerAttributes->{'media-default'});
    }

    public function testMediaReadyEncodes1setOfKeywords(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'media-ready'} = [
            \obray\ipp\enums\MediaSize::ISO_A4,
            \obray\ipp\enums\MediaSize::NA_LETTER,
        ];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'media-ready'});
        $this->assertSame(\obray\ipp\enums\MediaSize::ISO_A4,    (string) $decoded->{'media-ready'}[0]);
        $this->assertSame(\obray\ipp\enums\MediaSize::NA_LETTER, (string) $decoded->{'media-ready'}[1]);
    }

    // PWG5100.1 — finishings description attributes

    public function testFinishingsSupportedEncodes1setOfFinishingsEnum(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'finishings-supported'} = [
            \obray\ipp\enums\Finishings::none,
            \obray\ipp\enums\Finishings::staple,
            \obray\ipp\enums\Finishings::fold_half,
        ];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'finishings-supported'});
        $this->assertSame('none',      (string) $decoded->{'finishings-supported'}[0]);
        $this->assertSame('staple',    (string) $decoded->{'finishings-supported'}[1]);
        $this->assertSame('fold-half', (string) $decoded->{'finishings-supported'}[2]);
    }

    public function testFinishingsDefaultEncodes1setOfFinishingsEnum(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'finishings-default'} = \obray\ipp\enums\Finishings::none;

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertSame('none', (string) $decoded->{'finishings-default'});
    }

    public function testFinishingsReadyEncodes1setOfFinishingsEnum(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'finishings-ready'} = [
            \obray\ipp\enums\Finishings::staple,
            \obray\ipp\enums\Finishings::punch,
        ];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'finishings-ready'});
        $this->assertSame('staple', (string) $decoded->{'finishings-ready'}[0]);
        $this->assertSame('punch',  (string) $decoded->{'finishings-ready'}[1]);
    }

    // PWG5100.9 — Printer State Extensions

    public function testPwg5100Point9PrinterAttributesEncodeCorrectly(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'printer-uuid'} = 'urn:uuid:10000000-0000-1000-8000-000000000000';
        $printerAttributes->{'printer-state-change-time'} = 3600;
        $printerAttributes->{'printer-state-change-date-time'} = '2026-04-13 12:00:00.0+0000';
        $printerAttributes->{'printer-config-change-time'} = 1800;
        $printerAttributes->{'printer-supply-info-uri'} = 'https://printer.example/supplies';
        $printerAttributes->{'job-settable-attributes-supported'} = ['copies', 'sides'];
        $printerAttributes->{'printer-settable-attributes-supported'} = ['printer-info', 'printer-location'];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertSame('urn:uuid:10000000-0000-1000-8000-000000000000', (string) $decoded->{'printer-uuid'});
        $this->assertSame('3600', (string) $decoded->{'printer-state-change-time'});
        $this->assertSame('1800', (string) $decoded->{'printer-config-change-time'});
        $this->assertSame('https://printer.example/supplies', (string) $decoded->{'printer-supply-info-uri'});
        $this->assertIsArray($decoded->{'job-settable-attributes-supported'});
        $this->assertSame('copies', (string) $decoded->{'job-settable-attributes-supported'}[0]);
        $this->assertIsArray($decoded->{'printer-settable-attributes-supported'});
        $this->assertSame('printer-info', (string) $decoded->{'printer-settable-attributes-supported'}[0]);
    }

    // PWG5100.3 — Production Printing Attributes

    public function testPwg5100Point3PrinterDescriptionAttributesEncodeCorrectly(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'job-account-id-supported'} = true;
        $printerAttributes->{'job-accounting-user-id-supported'} = true;
        $printerAttributes->{'job-sheet-message-supported'} = false;
        $printerAttributes->{'multiple-document-handling-default'} = 'separate-documents-collated-copies';
        $printerAttributes->{'multiple-document-handling-supported'} = ['separate-documents-collated-copies', 'single-document'];
        $printerAttributes->{'output-device-supported'} = ['finisher-A', 'finisher-B'];
        $printerAttributes->{'page-delivery-default'} = 'same-order-face-down';
        $printerAttributes->{'page-delivery-supported'} = ['same-order-face-down', 'reverse-order-face-up'];
        $printerAttributes->{'page-order-received-default'} = '1-to-n-order';
        $printerAttributes->{'page-order-received-supported'} = ['1-to-n-order', 'n-to-1-order'];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertSame('true', (string) $decoded->{'job-account-id-supported'});
        $this->assertSame('true', (string) $decoded->{'job-accounting-user-id-supported'});
        $this->assertSame('false', (string) $decoded->{'job-sheet-message-supported'});
        $this->assertSame('separate-documents-collated-copies', (string) $decoded->{'multiple-document-handling-default'});
        $this->assertIsArray($decoded->{'multiple-document-handling-supported'});
        $this->assertSame('same-order-face-down', (string) $decoded->{'page-delivery-default'});
        $this->assertIsArray($decoded->{'page-delivery-supported'});
        $this->assertSame('1-to-n-order', (string) $decoded->{'page-order-received-default'});
        $this->assertIsArray($decoded->{'page-order-received-supported'});
    }

    // PWG5100.7 — IPP Job Extensions v2.0

    public function testPwg5100Point7PrinterDescriptionAttributesEncodeCorrectly(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'sheet-collate-default'} = 'collated';
        $printerAttributes->{'sheet-collate-supported'} = ['collated', 'uncollated'];
        $printerAttributes->{'job-error-action-default'} = 'abort-job';
        $printerAttributes->{'job-error-action-supported'} = ['abort-job', 'cancel-job', 'continue-job', 'suspend-job'];
        $printerAttributes->{'job-mandatory-attributes-supported'} = true;
        $printerAttributes->{'job-recipient-name-supported'} = false;
        $printerAttributes->{'imposition-template-default'} = 'none';
        $printerAttributes->{'imposition-template-supported'} = ['none', 'signature'];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertSame('collated', (string) $decoded->{'sheet-collate-default'});
        $this->assertIsArray($decoded->{'sheet-collate-supported'});
        $this->assertSame('abort-job', (string) $decoded->{'job-error-action-default'});
        $this->assertIsArray($decoded->{'job-error-action-supported'});
        $this->assertSame('true', (string) $decoded->{'job-mandatory-attributes-supported'});
        $this->assertSame('false', (string) $decoded->{'job-recipient-name-supported'});
        $this->assertSame('none', (string) $decoded->{'imposition-template-default'});
        $this->assertIsArray($decoded->{'imposition-template-supported'});
    }

    // PWG5100.11 — Job and Printer Extensions Set 2

    public function testPwg5100Point11PrinterDescriptionAttributesEncodeCorrectly(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->{'ipp-features-supported'} = ['ipp-everywhere', 'job-save'];
        $printerAttributes->{'printer-get-attributes-supported'} = ['document-description', 'job-template'];

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'ipp-features-supported'});
        $this->assertSame('ipp-everywhere', (string) $decoded->{'ipp-features-supported'}[0]);
        $this->assertSame('job-save', (string) $decoded->{'ipp-features-supported'}[1]);
        $this->assertIsArray($decoded->{'printer-get-attributes-supported'});
    }
}
