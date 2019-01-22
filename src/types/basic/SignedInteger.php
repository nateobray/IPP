<?php
namespace obray\ipp\types\basic;

class SignedInteger implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $value;
    protected $valueTag;
    private $length = 4;

    public function __construct($value=NULL)
    {
        if($value===NULL) return $this;
        $this->value = $value;
    }

    public function encode()
    {
        return pack('N',$this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $this->value = unpack('N',$binary,$offset)[1];
        return $this;
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