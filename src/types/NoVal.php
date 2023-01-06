<?php
namespace obray\ipp\types;

class NoVal implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $valueTag = 0x13;
    private $value = NULL;
    
    public function encode()
    {
        return;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getLength()
    {
        return 0;
    }

    public function decode($binary, $offset = 0, $length = NULL){
        return NULL;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return NULL;
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

}