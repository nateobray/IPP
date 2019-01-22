<?php
namespace obray\ipp\types\basic;

class SignedShort implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $valueTag;
    protected $value;
    private $length = 2;
    
    public function __construct($value=NULL)
    {
        if($value===NULL) return $this;
        $this->value = $value;
    }

    public function encode()
    {
        return pack('n',$this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $this->value = (unpack('n', $binary, $offset))[1];
        return $this;
    }

    public function len()
    {
        return $this->length;
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

    public function jsonSerialize()
    {
        return $this->value;
    }
}