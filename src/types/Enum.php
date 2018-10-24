<?php
namespace obray\ipp\types;

class Enum extends \obray\ipp\types\basic\SignedInteger
{
    public function __construct($code)
    {
        $constants = $this->getConstants();
        $key = array_search($code, $constants);
        if($key === false){
            throw new \Exception("Invalid operation specified.");
        }
        $this->key = $key;
        $this->value = $code;
    }

    public function __toString()
    {
        return $this->key;
    }

    public function getConstants()
    {
        $reflectionClass = new \ReflectionClass($this);
        return $reflectionClass->getConstants();
    }
}