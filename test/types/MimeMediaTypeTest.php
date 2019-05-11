<?php
$loader = require_once 'vendor/autoload.php';

class MimeMediaTypeTest extends \obray\ipp\test\types\basic\USASCIIStringTest
{
    public function testValueTag()
    {
        $integer = new \obray\ipp\types\MimeMediaType("application/json");
        $this->assertSame(0x49, $integer->getValueTag());
    }
}