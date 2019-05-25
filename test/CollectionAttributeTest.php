<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class CollectionAttributeTest extends TestCase
{
    public function testAttributeGroup()
    {
        $this->assertSame(1, 1);
    }
}