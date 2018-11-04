<?php
namespace obray\ipp\types\basic;

class SignedShort implements \obray\ipp\interfaces\TypeInterface
{
    protected $valueTag;
    protected $value;
    private $length = 2;
    
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function encode()
    {
        return pack('s',$this->value);
    }

    public function decode()
    {

    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}