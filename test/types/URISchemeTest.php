<?php
$loader = require_once 'vendor/autoload.php';

class URISchemeTest extends \obray\ipp\test\types\basic\USASCIIStringTest
{
    public function testValueTag()
    {
        $value = new \obray\ipp\types\URIScheme("http://www.blah.com");
        $this->assertSame(0x46, $value->getValueTag());
    }
}