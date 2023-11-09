<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class JobAttributeTest extends TestCase
{
    public function testAttributeGroup()
    {
        $this->assertSame(1, 1);
    }

    public function testJobStateReasonsCanBeSet() {

        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('job-state-reasons',new \obray\ipp\enums\JobStateReasons(\obray\ipp\enums\JobStateReasons::jobIncoming));

        $this->assertEquals('jobincoming',(string) $jobAttributes->{'job-state-reasons'});

    }

}