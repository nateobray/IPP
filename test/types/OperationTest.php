<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{
    public function testOperations()
    {
        $this->assertSame(\obray\ipp\types\Operation::PRINT_JOB, 0x0002);
        $this->assertSame(\obray\ipp\types\Operation::PRINT_URI, 0x0003);
        $this->assertSame(\obray\ipp\types\Operation::VALIDATE_JOB, 0x0004);
        $this->assertSame(\obray\ipp\types\Operation::CREATE_JOB, 0x0005);
        $this->assertSame(\obray\ipp\types\Operation::SEND_DOCUMENT, 0x0006);
        $this->assertSame(\obray\ipp\types\Operation::SEND_URI, 0x0007);
        $this->assertSame(\obray\ipp\types\Operation::CANCEL_JOB, 0x0008);
        $this->assertSame(\obray\ipp\types\Operation::GET_JOB_ATTRIBUTES, 0x0009);
        $this->assertSame(\obray\ipp\types\Operation::GET_JOBS, 0x000A);
        $this->assertSame(\obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES, 0x000B);
        $this->assertSame(\obray\ipp\types\Operation::HOLD_JOB, 0x000C);
        $this->assertSame(\obray\ipp\types\Operation::RELEASE_JOB, 0x000D);
        $this->assertSame(\obray\ipp\types\Operation::RESTART_JOB, 0x000E);
        $this->assertSame(\obray\ipp\types\Operation::PAUSE_PRINTER, 0x0010);
        $this->assertSame(\obray\ipp\types\Operation::RESUME_PRINTER, 0x0011);
        $this->assertSame(\obray\ipp\types\Operation::PURGE_JOBS, 0x0012);
    }
}