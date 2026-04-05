<?php
namespace obray\ipp\types\basic;

class SignedInteger implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $value;
    protected $valueTag;
    private $length = 4;

    public function __construct($value=NULL)
    {
        if($value===NULL) return $this;
        $this->value = $value;
    }

    public function encode()
    {
        // take our signed integer and convert to unsigned equivalent
        if($this->value < 0){ $this->value += 4294967296; }
        // pack into binary string as signed integer big endian byte order
        return pack('N',$this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $this->value = \obray\ipp\transport\DecodeGuard::unpack('Nvalue', $binary, $offset, 4, 'signed integer')['value'];
        // convert unsigned 32 bit integer into a signed integer
        if($this->value >= 2147483648) { $this->value -= 4294967296; }
        return $this;
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value;
    }
}
