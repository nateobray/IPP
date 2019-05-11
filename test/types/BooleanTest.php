<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    public function checkValueTag()
    {
        // value to encode and decode
        $value = true;
        // create new value type
        $si = new \obray\ipp\types\Boolean($value);
        // check value tag
        $this->assertSame($si->getValueTag(), 0x22);
    }

    public function testEncodeAndDecodeBooleanTrue()
    {
        // value to encode and decode
        $value = true;
        // create new value type
        $si = new \obray\ipp\types\Boolean($value);
        // test length (should be 1 bytes)
        $this->assertSame(1, $si->getLength());
        // encode value to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "01");
        // decode binary
        $decodedValue = $si->decode($binary)->getValue();
        // test if decoded value is same as original
        $this->assertSame($value, (boolean)$decodedValue);
    }

    public function testEncodeAndDecodeBooleanFalse()
    {
        // value to encode and decode
        $value = false;
        // create new value type
        $si = new \obray\ipp\types\Boolean($value);
        // test length (should be 1 bytes)
        $this->assertSame(1, $si->getLength());
        // encode value to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "00");
        // decode binary
        $decodedValue = $si->decode($binary)->getValue();
        // test if decoded value is same as original
        $this->assertSame($value, (boolean)$decodedValue);
    }

    public function testToString()
    {
        // integer to encode and decode
        $value = true;
        // create new integer
        $si = new \obray\ipp\types\Boolean($value);
        $this->assertSame('true', (string)$si);
    }

    public function testJasonSerialization()
    {
        // integer to encode and decode
        $value = false;
        // create new integer
        $si = new \obray\ipp\types\Boolean($value);
        $this->assertSame('false', json_encode($si));
    }
}