<?php
$loader = require_once 'vendor/autoload.php';

use obray\ipp\test\FakeRequest;
use PHPUnit\Framework\TestCase;

class CupsPrinterTest extends TestCase
{
    protected \obray\ipp\Printer $server;

    protected function setUp(): void
    {
        FakeRequest::reset();

        // CUPS server-level operations target the server URI, not a printer queue
        $this->server = (new \obray\ipp\Printer(
            'ipp://localhost/',
            'admin',
            'secret'
        ))->setRequestClass(FakeRequest::class);
    }

    public function testGetDefaultSendsCorrectOperation(): void
    {
        $this->server->getDefault(1);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_GET_DEFAULT, FakeRequest::$lastCall['operation']);
        $this->assertSame('ipp://localhost/', FakeRequest::$lastCall['printerURI']);
        $this->assertSame(1, FakeRequest::$lastCall['requestId']);
    }

    public function testGetDefaultWithRequestedAttributes(): void
    {
        $this->server->getDefault(2, ['printer-name', 'printer-state']);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_GET_DEFAULT, FakeRequest::$lastCall['operation']);
        $this->assertSame(2, FakeRequest::$lastCall['requestId']);
    }

    public function testGetPrintersSendsCorrectOperation(): void
    {
        $this->server->getPrinters(1);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_GET_PRINTERS, FakeRequest::$lastCall['operation']);
        $this->assertSame('ipp://localhost/', FakeRequest::$lastCall['printerURI']);
    }

    public function testGetPrintersWithLimit(): void
    {
        $this->server->getPrinters(1, null, 10);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_GET_PRINTERS, FakeRequest::$lastCall['operation']);
        $this->assertSame('10', (string) FakeRequest::$lastCall['operationAttributes']->{'limit'});
    }

    public function testGetPrintersWithRequestedAttributes(): void
    {
        $this->server->getPrinters(1, ['printer-name', 'printer-uri-supported']);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_GET_PRINTERS, FakeRequest::$lastCall['operation']);
    }

    public function testGetClassesSendsCorrectOperation(): void
    {
        $this->server->getClasses(1);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_GET_CLASSES, FakeRequest::$lastCall['operation']);
        $this->assertSame('ipp://localhost/', FakeRequest::$lastCall['printerURI']);
    }

    public function testGetClassesWithLimit(): void
    {
        $this->server->getClasses(1, null, 5);

        $this->assertSame(\obray\ipp\types\Operation::CUPS_GET_CLASSES, FakeRequest::$lastCall['operation']);
        $this->assertSame('5', (string) FakeRequest::$lastCall['operationAttributes']->{'limit'});
    }
}
