<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class CollectionAttributeTest extends TestCase
{
    private $collection;

    public function testAttributeGroup()
    {
        $this->collection = new \obray\ipp\CollectionAttribute("testCollection", array(
            "testAttribute1" => "testAttributeValue",
            "testAttribute2" => 10,
            "testAttribute3" => "Hello World"
        ));
        $this->assertInstanceOf(\obray\ipp\CollectionAttribute::class, $this->collection);
    }

    public function testEncode()
    {
        $this->collection = new \obray\ipp\CollectionAttribute("testCollection", array(
            "testAttribute1" => "testAttributeValue",
            "testAttribute2" => 10,
            "testAttribute3" => "Hello World"
        ));
        $binary = $this->collection->encode();
        $hex = bin2hex($binary);
        $this->assertSame($hex, "34000e74657374436f6c6c656374696f6e00004a0000000e746573744174747269627574653144000000127465737441747472696275746556616c75654a0000000e746573744174747269627574653221000000040000000a4a0000000e7465737441747472696275746533440000000b48656c6c6f20576f726c643700000000");
    }

    public function testDecode()
    {
        $collectionArrayIn = array(
            "testAttribute1" => "testAttributeValue",
            "testAttribute2" => 10,
            "testAttribute3" => "Hello World"
        );
        $this->collection = new \obray\ipp\CollectionAttribute("testCollection", $collectionArrayIn);
        $binary = $this->collection->encode();
        $decodedCollection = (new \obray\ipp\CollectionAttribute())->decode($binary);
        $this->assertSame(129, $decodedCollection->getLength());
        $collectionArrayOut = $decodedCollection->getValue();
        $diff = array_diff($collectionArrayIn, $collectionArrayOut);
        $this->assertEmpty($diff);
    }
}