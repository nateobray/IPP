<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class RangeOfIntegerTest extends TestCase
{
    public function testValueTag()
    {
        $value = new \obray\ipp\types\RangeOfInteger(100, 100);
        $this->assertSame(0x33, $value->getValueTag());
    }

    public function testEncodeAndDecodeRangeOfInteger()
    {
        // create new range object
        $range = new \obray\ipp\types\RangeOfInteger(100, 100);
        // encode range object
        $binary = $range->encode();
        // decode binary from first range object into new object
        $decodedRange = (new \obray\ipp\types\RangeOfInteger())->decode($binary);
        // compare the objects converted to strings
        $this->assertSame((string)$range, (string)$decodedRange);
    }

    public function testToString()
    {
        // create new range object
        $range = new \obray\ipp\types\RangeOfInteger(100, 100);
        // compare converstion to string
        $this->assertSame('100-100', (string)$range);
    }

    public function testJsonSerialization()
    {
        // create new range object
        $range = new \obray\ipp\types\RangeOfInteger(100, 100);
        // compare converstion to string
        $this->assertSame(json_encode('100-100'), json_encode($range));
    }
}