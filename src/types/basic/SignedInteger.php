<?php
namespace obray\ipp\types\basic;

class SignedInteger
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function encode()
    {
        print_r(unpack('i',$this->value));
        return implode('',unpack('i',$this->value));
    }

    public function decode()
    {

    }
}