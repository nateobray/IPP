<?php
declare(strict_types=1);

$loader = require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class PrinterCapabilitiesRequestStub
{
    public static \obray\ipp\transport\IPPPayload $payload;

    public static function send(string $printerURI, string $encodedPayload, ?string $user = null, ?string $password = null, array $curlOptions = []): \obray\ipp\transport\IPPPayload
    {
        return self::$payload;
    }
}

class PrinterCapabilitiesTest extends TestCase
{
    public function testGetSupportedMediaUsesMediaSupported()
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->set('media-supported', ['na_letter_8.5x11in', 'iso_a4_210x297mm']);

        PrinterCapabilitiesRequestStub::$payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES),
            new \obray\ipp\types\Integer(1),
            null,
            new \obray\ipp\OperationAttributes(),
            null,
            $printerAttributes
        );

        $printer = new \obray\ipp\Printer('ipp://localhost/printers/test', 'user', 'pass', [], PrinterCapabilitiesRequestStub::class);
        $media = $printer->getSupportedMedia();

        $this->assertSame(['na_letter_8.5x11in', 'iso_a4_210x297mm'], $media);
    }

    public function testGetSupportedResolutions()
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        $printerAttributes->set('printer-resolution-supported', ['300x300dpi', '600x600dpi']);

        PrinterCapabilitiesRequestStub::$payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES),
            new \obray\ipp\types\Integer(1),
            null,
            new \obray\ipp\OperationAttributes(),
            null,
            $printerAttributes
        );

        $printer = new \obray\ipp\Printer('ipp://localhost/printers/test', 'user', 'pass', [], PrinterCapabilitiesRequestStub::class);
        $resolutions = $printer->getSupportedResolutions();

        $this->assertSame(['300x300 dpi', '600x600 dpi'], $resolutions);
    }
}
