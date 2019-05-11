<?php
$loader = require_once 'vendor/autoload.php';

class TextWithLanguageTest extends \obray\ipp\test\types\NameWithLanguageTest
{
    public function testValueTag()
    {
        $value = new \obray\ipp\types\TextWithLanguage();
        $this->assertSame(0x35, $value->getValueTag());
    }
}