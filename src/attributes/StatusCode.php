<?php
namespace obray\ipp\attributes;

class StatusCode extends \obray\attributes\Attribute 
{
    protected $name = 'status-code';
    public function __construct($value)
    {
        $this->value = new \obray\ipp\enums\StatusCode($value);
        parent::__construct($this->value);
    }
   
}