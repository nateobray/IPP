<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class SignedShortTest extends TestCase
{
    protected $printer;

    public function testEncodeAndDecodePositiveSignedInteger()
    {
        // integer to encode and decode
        $integer = 101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedShort($integer);
        // test length (should be 4 bytes)
        $this->assertSame(2, $si->getLength());
        // encode integer to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "0065");
        // decode binary
        $decodedInt = $si->decode($binary)->getValue();
        // test if decoded value is same as original
        $this->assertSame($integer, $decodedInt);
    }

    public function testEncodeAndDecodeNegativeSignedInteger()
    {
        // integer to encode and decode
        $integer = -101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedShort($integer);
        // test length (should be 4 bytes)
        $this->assertSame(2, $si->getLength());
        // encode integer to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "ff9b");
        // decode binary
        $decodedInt = $si->decode($binary)->getValue();
        // test if decoded value is same as original
        $this->assertSame($integer, $decodedInt);
    }

    public function testToString()
    {
        // integer to encode and decode
        $integer = 101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedShort($integer);
        $this->assertSame('101', (string)$si);
    }

    public function testJasonSerialization()
    {
        // integer to encode and decode
        $integer = 101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedShort($integer);
        $this->assertSame('101', json_encode($si));
    }
}