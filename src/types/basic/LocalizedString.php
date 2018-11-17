<?php
namespace obray\ipp\types\basic;

class LocalizedString implements \obray\ipp\interfaces\TypeInterface
{
    protected $value;
    protected $valueTag;
    private $length;

    public function __construct($value)
    {
        $this->length = strlen($value);
        $this->value = $value;
    }

    public function encode()
    {
        return pack('a'.($this->length), $this->value);
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

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}