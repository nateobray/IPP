<?php
namespace obray\ipp\types;

class Boolean extends \obray\ipp\types\basic\SignedByte
{
    protected $valueTag = 0x22;

    public function getValue()
    {
        return ($this->value==0)?false:true;
    }

    public function __toString()
    {
        return ($this->value==0)?'false':'true';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ($this->value==0)?false:true;
    }
}