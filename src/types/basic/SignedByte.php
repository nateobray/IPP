<?php
namespace obray\ipp\types\basic;

class SignedByte
{
    private $value;

    public function __construct($value)
    {
        if(strlen($value)>1){
            throw new \Exception("Invalid signed byte.");
        }
        $this->value = $value;
    }

    public function encode()
    {
        return \upack('c',$this->value);
    }

    public function decode()
    {
        
    }
}