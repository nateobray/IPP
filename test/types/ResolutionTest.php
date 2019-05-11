<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class ResolutionTest extends TestCase
{
    public function testValueTag()
    {
        $value = new \obray\ipp\types\Resolution(100, 100, 3);
        $this->assertSame(0x32, $value->getValueTag());
    }

    public function testEncodeAndDecodeResolution()
    {
        // create new range object
        $range = new \obray\ipp\types\Resolution(100, 100, 3);
        // encode range object
        $binary = $range->encode();
        // decode binary from first range object into new object
        $decodedRange = (new \obray\ipp\types\Resolution())->decode($binary);
        // compare the objects converted to strings
        $this->assertSame((string)$range, (string)$decodedRange);
    }

    public function testToString()
    {
        // create new range object
        $range = new \obray\ipp\types\Resolution(100, 100, 3);
        // compare converstion to string
        $this->assertSame('100x100 dpi', (string)$range);
    }

    public function testJsonSerialization()
    {
        // create new range object
        $range = new \obray\ipp\types\Resolution(100, 100, 3);
        // compare converstion to string
        $this->assertSame(json_encode('100x100 dpi'), json_encode($range));
    }
}