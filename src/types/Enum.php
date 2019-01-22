<?php
namespace obray\ipp\types;

class Enum extends \obray\ipp\types\basic\SignedShort implements \JsonSerializable
{
    protected $valueTag = 0x23;
    protected $key;
    protected $value;

    public function __construct($code=NULL)
    {
        if($code!==NULL){
            $constants = $this->getConstants();
            $key = array_search($code, $constants);
            if($key === false){
                throw new \Exception("Invalid operation specified.");
            }
            $this->key = $key;
            $this->value = $code;
        }
    }

    public function decode($binary, $offset=0, $length=NULL){
        $this->value = (unpack('N', $binary, $offset))[1];
        $constants = $this->getConstants();
        $this->key = array_search($this->value, $constants);
        return $this;
    }

    public function __toString()
    {
        return str_replace('_','-',strtolower($this->key));
    }

    public function getConstants()
    {
        $reflectionClass = new \ReflectionClass($this);
        return $reflectionClass->getConstants();
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function jsonSerialize()
    {
        return str_replace('_','-',strtolower($this->key));
    }
}