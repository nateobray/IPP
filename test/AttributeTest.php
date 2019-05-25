<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testTypes()
    {
        //$unsupported = new \obray\ipp\Attribute('test', NULL,  0x10);

        // test unknown value
        $unknown = new \obray\ipp\Attribute('test', '',  0x12);
        $this->assertInstanceOf(\obray\ipp\types\Unknown::class, $unknown->getAttributeValueClass());
        $this->assertSame('unknown', $unknown->getAttributeValue());

        // test no value
        $noVal = new \obray\ipp\Attribute('test', '',  0x13);
        $this->assertInstanceOf(\obray\ipp\types\NoVal::class, $noVal->getAttributeValueClass());
        $this->assertSame(NULL, $noVal->getAttributeValue());

        // test boolean
        $boolean = new \obray\ipp\Attribute('test', true,  0x22);
        $this->assertInstanceOf(\obray\ipp\types\Boolean::class, $boolean->getAttributeValueClass());
        $this->assertSame(true, $boolean->getAttributeValue());

        // test charset
        $charset = new \obray\ipp\Attribute('test', 'utf-8',  0x47);
        $this->assertInstanceOf(\obray\ipp\types\Charset::class, $charset->getAttributeValueClass());
        $this->assertSame('utf-8', $charset->getAttributeValue());

        // test datetime
        $datetime = new \obray\ipp\Attribute('test', '2019-05-18 23:45:32.4-0700',  0x31);
        $this->assertInstanceOf(\obray\ipp\types\DateTime::class, $datetime->getAttributeValueClass());
        $this->assertSame('2019-05-18 23:45:32.400-0700', (string)$datetime->getAttributeValue());

        // test enum
        $enum = new \obray\ipp\Attribute('test', 25,  0x23);
        $this->assertInstanceOf(\obray\ipp\types\Integer::class, $enum->getAttributeValueClass());
        $this->assertSame(25, $enum->getAttributeValue());

        // test integer
        $enum = new \obray\ipp\Attribute('test', 105,  0x21);
        $this->assertInstanceOf(\obray\ipp\types\Integer::class, $enum->getAttributeValueClass());
        $this->assertSame(105, $enum->getAttributeValue());

        // test keyword
        $keyword = new \obray\ipp\Attribute('test', 'keyword',  0x44);
        $this->assertInstanceOf(\obray\ipp\types\Keyword::class, $keyword->getAttributeValueClass());
        $this->assertSame('keyword', $keyword->getAttributeValue());

        // test keyword
        $mime = new \obray\ipp\Attribute('test', 'mime-media-type',  0x49);
        $this->assertInstanceOf(\obray\ipp\types\MimeMediaType::class, $mime->getAttributeValueClass());
        $this->assertSame('mime-media-type', $mime->getAttributeValue());

        // test name
        $name = new \obray\ipp\Attribute('test', 'test-name',  0x0008);
        $this->assertInstanceOf(\obray\ipp\types\NameWithoutLanguage::class, $name->getAttributeValueClass());
        $this->assertSame('test-name', $name->getAttributeValue());

        // test natural language
        $naturalLanguage = new \obray\ipp\Attribute('test', 'en',  0x48);
        $this->assertInstanceOf(\obray\ipp\types\NaturalLanguage::class, $naturalLanguage->getAttributeValueClass());
        $this->assertSame('en', $naturalLanguage->getAttributeValue());

        // test octet string
        $octetString = new \obray\ipp\Attribute('test', 'test-octet-string',  0x30);
        $this->assertInstanceOf(\obray\ipp\types\OctetString::class, $octetString->getAttributeValueClass());
        $this->assertSame('test-octet-string', $octetString->getAttributeValue());

        // test range of integer
        $rangeOfInteger = new \obray\ipp\Attribute('test', '100-500',  0x33);
        $this->assertInstanceOf(\obray\ipp\types\RangeOfInteger::class, $rangeOfInteger->getAttributeValueClass());
        $this->assertSame('100-500', $rangeOfInteger->getAttributeValue());

        // test text with language
        $textWithLanguage = new \obray\ipp\Attribute('test', 'some test text',  0x35, NULL, 'en');
        $this->assertInstanceOf(\obray\ipp\types\TextWithLanguage::class, $textWithLanguage->getAttributeValueClass());
        $this->assertSame('some test text', (string)$textWithLanguage->getAttributeValue());

        // test name with language
        $nameWithLanguage = new \obray\ipp\Attribute('test', 'some test text',  0x36, NULL, 'en');
        $this->assertInstanceOf(\obray\ipp\types\NameWithLanguage::class, $nameWithLanguage->getAttributeValueClass());
        $this->assertSame('some test text', (string)$nameWithLanguage->getAttributeValue());

        // test text without language
        $textWithoutLanguage = new \obray\ipp\Attribute('test', 'some test text',  0x41);
        $this->assertInstanceOf(\obray\ipp\types\TextWithoutLanguage::class, $textWithoutLanguage->getAttributeValueClass());
        $this->assertSame('some test text', (string)$textWithoutLanguage->getAttributeValue());

        // test name without language
        $nameWithoutLanguage = new \obray\ipp\Attribute('test', 'some test text',  0x42);
        $this->assertInstanceOf(\obray\ipp\types\NameWithoutLanguage::class, $nameWithoutLanguage->getAttributeValueClass());
        $this->assertSame('some test text', (string)$nameWithoutLanguage->getAttributeValue());

        // test range of integer
        $resolution = new \obray\ipp\Attribute('test', '300x600 dpi',  0x32);
        $this->assertInstanceOf(\obray\ipp\types\Resolution::class, $resolution->getAttributeValueClass());
        $this->assertSame('300x600 dpi', $resolution->getAttributeValue());

        // test uri
        $uri = new \obray\ipp\Attribute('test', 'http://www.google.com',  0x45);
        $this->assertInstanceOf(\obray\ipp\types\URI::class, $uri->getAttributeValueClass());
        $this->assertSame('http://www.google.com', $uri->getAttributeValue());

        // test scheme
        $uriScheme = new \obray\ipp\Attribute('test', 'http://www.google.com',  0x46);
        $this->assertInstanceOf(\obray\ipp\types\URIScheme::class, $uriScheme->getAttributeValueClass());
        $this->assertSame('http://www.google.com', $uriScheme->getAttributeValue());

    }
}