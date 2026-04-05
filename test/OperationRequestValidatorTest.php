<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class OperationRequestValidatorTest extends TestCase
{
    public function testValidatorRejectsOutOfOrderTargetAttributes(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'requested-attributes'} = ['printer-name'];
        $operationAttributes->{'printer-uri'} = 'ipp://localhost/printers/CUPS_PDF';

        $this->expectException(\obray\ipp\exceptions\InvalidRequest::class);
        $this->expectExceptionMessage('Get-Printer-Attributes requires leading target attributes');

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES,
            $operationAttributes
        );
    }

    public function testValidatorAcceptsJobUriTargetForm(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'job-uri'} = 'ipp://localhost/jobs/123';

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::GET_JOB_ATTRIBUTES,
            $operationAttributes
        );

        $this->assertTrue(true);
    }

    public function testValidatorRejectsRedundantPrinterUriWhenUsingJobUriTargetForm(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = 'ipp://localhost/printers/CUPS_PDF';
        $operationAttributes->{'job-uri'} = 'ipp://localhost/jobs/123';

        $this->expectException(\obray\ipp\exceptions\InvalidRequest::class);
        $this->expectExceptionMessage('Get-Job-Attributes requires leading target attributes');

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::GET_JOB_ATTRIBUTES,
            $operationAttributes
        );
    }
}
