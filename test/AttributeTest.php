<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testAttribute()
    {
        $this->assertSame(1, 1);
    }
}