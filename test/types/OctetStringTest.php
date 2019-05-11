<?php
$loader = require_once 'vendor/autoload.php';

class OctetStringTest extends \obray\ipp\test\types\basic\OctetStringTest
{
    public function testValueTag()
    {
        $value = new \obray\ipp\types\OctetString("natural-language");
        $this->assertSame(0x30, $value->getValueTag());
    }
}