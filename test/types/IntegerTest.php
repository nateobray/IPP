<?php
$loader = require_once 'vendor/autoload.php';

class IntegerTest extends \obray\ipp\test\types\basic\SignedIntegerTest
{
    public function testValueTag()
    {
        $integer = new \obray\ipp\types\Integer(100);
        $this->assertSame(0x21, $integer->getValueTag());
    }
}