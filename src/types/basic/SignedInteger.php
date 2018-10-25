<?php
namespace obray\ipp\types\basic;

class SignedInteger implements \obray\ipp\interfaces\TypeInterface
{
    protected $value;
    protected $valueTag;
    private $length = 4;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function encode()
    {
        print_r("encoding: ".$this->value."\n");
        return pack('l',$this->value);
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
}