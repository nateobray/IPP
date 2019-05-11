<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class SignedByte extends TestCase
{
    public function testEncodeAndDecodeCharSignedByte()
    {
        // integer to encode and decode
        $integer = 'B';
        // create new integer
        $si = new \obray\ipp\types\basic\SignedByte($integer);
        // test length (should be 4 bytes)
        $this->assertSame(1, $si->getLength());
        // encode integer to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "42");
        // decode binary
        $decodedInt = $si->decode($binary)->getValue();
        // test if decoded value is same as original
        $this->assertSame($integer, chr($decodedInt));
    }

    public function testEncodeAndDecodePositiveSignedByte()
    {
        // integer to encode and decode
        $integer = 101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedByte($integer);
        // test length (should be 4 bytes)
        $this->assertSame(1, $si->getLength());
        // encode integer to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "65");
        // decode binary
        $decodedInt = $si->decode($binary)->getValue();
        // test if decoded value is same as original
        $this->assertSame($integer, $decodedInt);
    }

    public function testEncodeAndDecodeNegativeSignedByte()
    {
        // integer to encode and decode
        $integer = -101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedByte($integer);
        // test length (should be 4 bytes)
        $this->assertSame(1, $si->getLength());
        // encode integer to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame("9b", $hex);
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
        $si = new \obray\ipp\types\basic\SignedByte($integer);
        $this->assertSame('101', (string)$si);
    }

    public function testJasonSerialization()
    {
        // integer to encode and decode
        $integer = 101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedByte($integer);
        $this->assertSame('101', json_encode($si));
    }
}