<?php
namespace obray\ipp\types;

class Type
{
    private $key;
    protected $value;

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