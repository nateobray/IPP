<?php
namespace obray\ipp\test\types;
$loader = require_once 'vendor/autoload.php';

class TextWithoutLanguageTest extends \obray\ipp\test\types\NameWithoutLanguageTest
{
    public function testValueTag()
    {
        $integer = new \obray\ipp\types\TextWithoutLanguage("name");
        $this->assertSame(0x41, $integer->getValueTag());
    }
}