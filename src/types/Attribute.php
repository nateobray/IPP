<?php
namespace obray\ipp\types;

class Attribute
{
    protected $name;
    private $valueTag;
    private $nameLength;
    private $valueLength;
    private $value;

    public function __construct($name, \obray\ipp\interfaces\TypeInterface $value)
    {
        $this->valueTag = $value->getValueTag();
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($this->name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($this->name);
        $this->valueLength = new \obray\ipp\types\basic\SignedShort($value->getLength());
        $this->value = $value;
    }

    public function encode()
    {
        $binary = pack('c',$this->valueTag);
        $binary .= $this->nameLength->encode();
        $binary .= $this->name->encode();
        $binary .= $this->valueLength->encode();
        $binary .= $this->value->encode();
        return $binary;
    }

    public function decode()
    {

    }
}