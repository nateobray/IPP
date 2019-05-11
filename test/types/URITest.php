<?php
$loader = require_once 'vendor/autoload.php';

class URITest extends \obray\ipp\test\types\basic\USASCIIStringTest
{
    public function testValueTag()
    {
        $value = new \obray\ipp\types\URI("http://www.blah.com");
        $this->assertSame(0x45, $value->getValueTag());
    }
}