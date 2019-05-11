<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class CharsetTest extends TestCase
{
    public function checkValueTag()
    {
        // create new value type
        $si = new \obray\ipp\types\Charset('utf-8');
        // check value tag
        $this->assertSame($si->getValueTag(), 0x47);
    }

    public function testEncodeAndDecodeCharset()
    {
        // value to encode and decode
        $value = 'utf-8';
        // create new value type
        $si = new \obray\ipp\types\Charset($value);
        // test length
        $this->assertSame(5, $si->getLength());
        // encode value to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "7574662d38");
        // decode binary
        $decodedValue = $si->decode($binary, 0, $si->getLength())->getValue();
        // test if decoded value is same as original
        $this->assertSame($value, (string)$decodedValue);
    }

    public function testToString()
    {
        // integer to encode and decode
        $value = 'utf-8';
        // create new integer
        $si = new \obray\ipp\types\Charset($value);
        $this->assertSame($value, (string)$si);
    }

    public function testJasonSerialization()
    {
        // integer to encode and decode
        $value = 'utf-8';
        // create new integer
        $si = new \obray\ipp\types\Charset($value);
        $this->assertSame(json_encode($value), json_encode($si));
    }
}