<?php
$loader = require_once 'vendor/autoload.php';

class NaturalLanguageTest extends \obray\ipp\test\types\basic\USASCIIStringTest
{
    public function testValueTag()
    {
        $value = new \obray\ipp\types\NaturalLanguage("natural-language");
        $this->assertSame(0x48, $value->getValueTag());
    }
}