<?php
namespace obray\ipp\test\types;
$loader = require_once 'vendor/autoload.php';

class NameWithoutLanguageTest extends \obray\ipp\test\types\basic\LocalizedStringTest
{
    public function testValueTag()
    {
        $integer = new \obray\ipp\types\NameWithoutLanguage("name");
        $this->assertSame(0x42, $integer->getValueTag());
    }
}