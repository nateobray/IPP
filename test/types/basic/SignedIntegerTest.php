<?php
namespace obray\ipp\test\types\basic;
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class SignedIntegerTest extends TestCase
{
    public function testEncodeAndDecodePositiveSignedInteger()
    {
        // integer to encode and decode
        $integer = 101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedInteger($integer);
        // test length (should be 4 bytes)
        $this->assertSame(4, $si->getLength());
        // encode integer to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame($hex, "00000065");
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
        $si = new \obray\ipp\types\basic\SignedInteger($integer);
        // test length (should be 4 bytes)
        $this->assertSame(4, $si->getLength());
        // encode integer to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame("ffffff9b", $hex);
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
        $si = new \obray\ipp\types\basic\SignedInteger($integer);
        $this->assertSame('101', (string)$si);
    }

    public function testJasonSerialization()
    {
        // integer to encode and decode
        $integer = 101;
        // create new integer
        $si = new \obray\ipp\types\basic\SignedInteger($integer);
        $this->assertSame('101', json_encode($si));
    }
}