<?php
namespace obray\ipp\types;

class Attribute
{
    private $valueTag;
    private $nameLength;
    private $name;
    private $valueLength;
    private $value;

    public function __construct(string $name, \obray\ipp\interfaces\TypeInterface $value)
    {
        $this->valueTag = $value->getValueTag();
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($name);
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