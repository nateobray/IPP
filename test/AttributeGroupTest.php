<?php

namespace obray\ipp\test;

use obray\ipp\AttributeGroup;
use obray\ipp\types\Integer;
use PHPUnit\Framework\TestCase;

class TestAttributes extends AttributeGroup{

    public function set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }
}

class AttributeGroupTest extends TestCase
{

    private ?TestAttributes $testAttributes = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->testAttributes = new TestAttributes();
        $this->testAttributes->set('test',new Integer(255));
        $this->testAttributes->set('empty','');

    }

    public function testHas() {

        $this->assertTrue($this->testAttributes->has('test'));
        $this->assertFalse($this->testAttributes->has('non-existent'));
        $this->assertFalse($this->testAttributes->has('empty'));

    }
}
