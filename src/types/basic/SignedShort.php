<?php
namespace obray\ipp\types\basic;

class SignedShort implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $valueTag;
    protected $value;
    private $length = 2;
    
    public function __construct($value=NULL)
    {
        if($value===NULL) return $this;
        $this->value = $value;
    }

    public function encode()
    {
        // take our signed integer and convert to unsigned equivalent
        if($this->value < 0){ $this->value += 65536; }
        // pack into binary string as signed integer big endian byte order
        return pack('n',$this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        // unpack as unsigned short (no way to pull out signed short with correct byte order using unpack)
        $this->value = (unpack('n', $binary, $offset))[1];
        // convert unsigned short into a signed short
        if($this->value >= 32768) { $this->value -= 65536; }
        return $this;
    }

    public function len()
    {
        return $this->length;
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

    public function jsonSerialize()
    {
        return $this->value;
    }
}