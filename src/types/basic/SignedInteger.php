<?php
namespace obray\ipp\types\basic;

class SignedInteger implements \obray\ipp\interfaces\TypeInterface
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
        print_r("encoding: ".$this->value."\n");
        return pack('N',$this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
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