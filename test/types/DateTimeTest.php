<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function checkValueTag()
    {
        // create new value type
        $si = new \obray\ipp\types\DateTime('2019-05-4 15:21:45');
        // check value tag
        $this->assertSame($si->getValueTag(), 0x31);
    }

    public function testEncodeAndDecodeDateTime()
    {
        // value to encode and decode
        $value = '2019-05-04 15:21:45.545-0700';
        // create new value type
        $si = new \obray\ipp\types\DateTime($value);
        // encode value to binary
        $binary = $si->encode();
        // convert binary to hex
        $hex = bin2hex($binary);
        // test if hex is correct
        $this->assertSame("07e305040f152d052d0700", $hex);
        // decode binary
        $decodedValue = (new \obray\ipp\types\DateTime())->decode($binary);
        // test if decoded value is same as original except rounding the milliseconds to deciseconds
        // the binary encoded value only supports deciseconds according to RFC1903
        $this->assertSame('2019-05-04 15:21:45.500-0700', (string)$decodedValue);
    }

    public function testToString()
    {
        // Date value to encode
        $value = '2019-05-04 15:21:45.5-0700';
        // Create new DateTime object
        $si = new \obray\ipp\types\DateTime($value);
        // Convert to string and compare
        $this->assertSame('2019-05-04 15:21:45.500-0700', (string)$si);
    }

    public function testJasonSerialization()
    {
        // Date value to encode
        $value = '2019-05-04 15:21:45.545-0700';
        // Create new DateTime object
        $si = new \obray\ipp\types\DateTime($value);
        // json encode and compare
        $this->assertSame(json_encode($value), json_encode($si));
    }
}