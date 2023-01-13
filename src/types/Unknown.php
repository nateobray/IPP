<?php
namespace obray\ipp\types;

class Unknown implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $valueTag = 0x13;
    private $value = 'unknown';
    private $length = 0;
    
    public function encode()
    {
        return;
    }

    public function decode($binary, $offset = 0, $length = NULL){
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (string)$this;
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function getLength()
    {
        return $this->length;
    }

}