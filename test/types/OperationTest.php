<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{
    public function testOperations()
    {
        $this->assertSame(\obray\ipp\types\Operation::printJob, 0x0002);
        $this->assertSame(\obray\ipp\types\Operation::printURI, 0x0003);
        $this->assertSame(\obray\ipp\types\Operation::validateJob, 0x0004);
        $this->assertSame(\obray\ipp\types\Operation::createJob, 0x0005);
        $this->assertSame(\obray\ipp\types\Operation::sendDocument, 0x0006);
        $this->assertSame(\obray\ipp\types\Operation::sendURI, 0x0007);
        $this->assertSame(\obray\ipp\types\Operation::cancelJob, 0x0008);
        $this->assertSame(\obray\ipp\types\Operation::getJobAttributes, 0x0009);
        $this->assertSame(\obray\ipp\types\Operation::getJobs, 0x000A);
        $this->assertSame(\obray\ipp\types\Operation::getPrinterAttributes, 0x000B);
        $this->assertSame(\obray\ipp\types\Operation::holdJob, 0x000C);
        $this->assertSame(\obray\ipp\types\Operation::releaseJob, 0x000D);
        $this->assertSame(\obray\ipp\types\Operation::restartJob, 0x000E);
        $this->assertSame(\obray\ipp\types\Operation::pausePrinter, 0x0010);
        $this->assertSame(\obray\ipp\types\Operation::resumePrinter, 0x0011);
        $this->assertSame(\obray\ipp\types\Operation::purgeJobs, 0x0012);
    }
}