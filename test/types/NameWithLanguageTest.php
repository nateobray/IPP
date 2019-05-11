<?php
namespace obray\ipp\test\types;
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class NameWithLanguageTest extends TestCase
{
    public function testValueTag()
    {
        // create new value type
        $si = new \obray\ipp\types\NameWithLanguage('en', 'name');
        // check value tag
        $this->assertSame($si->getValueTag(), 0x36);
    }

    public function testEncodeAndDecodeNameWithLanguage()
    {
        // create new value type
        $si = new \obray\ipp\types\NameWithLanguage('en', 'name');
        // encode value to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame("0002656e00046e616d65", $hex);
        // decode binary
        $decodedValue = (new \obray\ipp\types\NameWithLanguage())->decode($binary);
        // test if decoded value is same as original
        $this->assertSame('name', (string)$decodedValue);
    }

    public function testLength()
    {
        // create new value type
        $si = new \obray\ipp\types\NameWithLanguage('en', 'name');
        // test length
        $this->assertSame(4+2+4, $si->getLength());
    }

    public function testToString()
    {
        // create new value type
        $si = new \obray\ipp\types\NameWithLanguage('en', 'name');
        // test
        $this->assertSame('name', (string)$si);
    }

    public function testJasonSerialization()
    {
        // create new value type
        $si = new \obray\ipp\types\NameWithLanguage('en', 'name');
        // test
        $this->assertSame(json_encode('name'), json_encode($si));
    }
    
}