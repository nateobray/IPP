<?php
$loader = require_once 'vendor/autoload.php';

class KeywordTest extends \obray\ipp\test\types\basic\USASCIIStringTest
{
    public function testValueTag()
    {
        $integer = new \obray\ipp\types\Keyword("keyword");
        $this->assertSame(0x44, $integer->getValueTag());
    }
}