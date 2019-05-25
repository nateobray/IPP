<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    public function testAttributeGroup()
    {
        $this->assertSame(1, 1);
    }
}