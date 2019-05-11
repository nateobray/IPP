<?php
namespace obray\ipp\test\types\basic;
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class USASCIIStringTest extends TestCase
{
    public function testEncodeAndDecodeLocalizedString()
    {
        // string to encode and decode
        $string = 'A quick brown fox jumped over the fence.';
        // create new octet string
        $si = new \obray\ipp\types\basic\USASCIIString($string);
        // test length
        $this->assertSame($si->getLength(), strlen($string));
        // encode string to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame("4120717569636b2062726f776e20666f78206a756d706564206f766572207468652066656e63652e", $hex);
        // decode binary
        $decodedString = $si->decode($binary, 0, $si->getLength())->getValue();
        // test if decoded value is same as original
        $this->assertSame($string, $decodedString);
    }

    public function testToString()
    {
        // string to encode and decode
        $string = 'A quick brown fox jumped over the fence.';
        // create new octet string
        $si = new \obray\ipp\types\basic\USASCIIString($string);
        $this->assertSame($string, (string)$si);
    }

    public function testJasonSerialization()
    {
        // string to encode and decode
        $string = 'A quick brown fox jumped over the fence.';
        // create new octet string
        $si = new \obray\ipp\types\basic\USASCIIString($string);
        $this->assertSame(json_encode($string), json_encode($si));
    }
}