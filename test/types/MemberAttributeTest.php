<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class MemberAttributeTest extends TestCase
{
    private $collection;

    public function testMemberattribute()
    {
        // test text member attribute
        $this->memberAttribute = new \obray\ipp\types\MemberAttribute("TestAttribute", "text");
        $this->assertInstanceOf(\obray\ipp\types\MemberAttribute::class, $this->memberAttribute);

        // test numeric member attribute
        $this->memberAttribute = new \obray\ipp\types\MemberAttribute("TestAttribute", 133224);
        $this->assertInstanceOf(\obray\ipp\types\MemberAttribute::class, $this->memberAttribute);

    }

    public function testEncode()
    {
        $binary = (new \obray\ipp\types\MemberAttribute("TestAttribute", "text"))->encode();
        $hex = bin2hex($binary);
        $this->assertSame($hex, "4a0000000d54657374417474726962757465440000000474657874");
    }

    public function testDecode()
    {
        // test numeric
        $binary = (new \obray\ipp\types\MemberAttribute("TestAttribute", 10))->encode();
        $decodedMember = (new \obray\ipp\types\MemberAttribute())->decode($binary);
        $this->assertSame($decodedMember->getKey(), "TestAttribute");
        $this->assertSame($decodedMember->getValue(), 10);
        // test text
        $binary = (new \obray\ipp\types\MemberAttribute("TestAttribute", "This is awesome!!"))->encode();
        $decodedMember = (new \obray\ipp\types\MemberAttribute())->decode($binary);
        $this->assertSame($decodedMember->getKey(), "TestAttribute");
        $this->assertSame($decodedMember->getValue(), "This is awesome!!");

    }
}