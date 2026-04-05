<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class OperationAttributesTest extends TestCase
{
    public function testOperationAttributesDefaultToUtf8AndEnglish(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();

        $this->assertSame('utf-8', (string) $operationAttributes->{'attributes-charset'});
        $this->assertSame('en', (string) $operationAttributes->{'attributes-natural-language'});
    }

    public function testOperationAttributesSupportIpp11RequestFields(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->setNaturalLanguage('fr-ca');
        $operationAttributes->{'printer-uri'} = 'ipp://printer.example/printers/main';
        $operationAttributes->{'status-message'} = 'Pret';
        $operationAttributes->{'which-jobs'} = 'completed';
        $operationAttributes->{'limit'} = 25;
        $operationAttributes->{'my-jobs'} = true;
        $operationAttributes->{'last-document'} = false;

        $this->assertSame('ipp://printer.example/printers/main', (string) $operationAttributes->{'printer-uri'});
        $this->assertSame('Pret', (string) $operationAttributes->{'status-message'});
        $this->assertSame('completed', (string) $operationAttributes->{'which-jobs'});
        $this->assertSame('25', (string) $operationAttributes->{'limit'});
        $this->assertSame('true', (string) $operationAttributes->{'my-jobs'});
        $this->assertSame('false', (string) $operationAttributes->{'last-document'});
    }

    public function testRequestedAttributesRoundTripAsMultiValueKeyword(): void
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'requested-attributes'} = ['printer-name', 'printer-state'];

        $encoded = $operationAttributes->encode();
        $decoded = new \obray\ipp\OperationAttributes();
        $offset = 0;
        $decoded->decode($encoded, $offset);

        $this->assertTrue($decoded->has('requested-attributes'));
        $this->assertIsArray($decoded->{'requested-attributes'});
        $this->assertCount(2, $decoded->{'requested-attributes'});
        $this->assertSame('printer-name', (string) $decoded->{'requested-attributes'}[0]);
        $this->assertSame('printer-state', (string) $decoded->{'requested-attributes'}[1]);
    }

    public function testValidateRejectsNonUtf8Charsets(): void
    {
        $this->expectException(\obray\ipp\exceptions\ClientErrorCharsetNotSupported::class);

        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'attributes-charset'} = 'us-ascii';

        $operationAttributes->validate([]);
    }

    public function testValidateRejectsMissingNaturalLanguage(): void
    {
        $this->expectException(\obray\ipp\exceptions\InvalidRequest::class);

        $operationAttributes = new class extends \obray\ipp\OperationAttributes {
            public function removeAttribute(string $name): void
            {
                unset($this->attributes[$name]);
            }
        };
        $operationAttributes->removeAttribute('attributes-natural-language');

        $operationAttributes->validate([]);
    }
}
