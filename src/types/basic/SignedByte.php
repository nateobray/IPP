<?php
namespace obray\ipp\types\basic;

class SignedByte implements \obray\ipp\interfaces\TypeInterface
{
    protected $value;
    protected $valueTag;
    private $length = 1;

    public function __construct($value)
    {
        if(strlen($value)>1){
            throw new \Exception("Invalid signed byte.");
        }
        $this->value = $value;
    }

    public function encode()
    {
        return pack('c',$this->value);
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