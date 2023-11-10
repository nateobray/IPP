<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class JobAttributeTest extends TestCase
{
    public function testAttributeGroup()
    {
        $this->assertSame(1, 1);
    }

    public function testInstantiationWithoutArrayOfAttributes() {

        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('page-ranges','1-2');

        $this->assertEquals(new \obray\ipp\types\RangeOfInteger(1,2),$jobAttributes->{'page-ranges'}->getAttributeValueClass());

    }

    public function testInstantiationWithArrayOfAttributes() {

        $attributes = [
            'job-uri' => 'ipps://www.cups.org/ipp/2234451',
            'page-ranges' => '3-4'
        ];

        $jobAttributes = new \obray\ipp\JobAttributes($attributes);

        $this->assertEquals(new \obray\ipp\types\URI('ipps://www.cups.org/ipp/2234451'),$jobAttributes->{'job-uri'}->getAttributeValueClass());
        $this->assertEquals(new \obray\ipp\types\RangeOfInteger(3,4),$jobAttributes->{'page-ranges'}->getAttributeValueClass());

    }
}