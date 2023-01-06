<?php
namespace obray\ipp\types\basic;

class OctetString implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $value;
    protected $valueTag;
    private $length;

    public function __construct($value=NULL)
    {
        if($value===NULL) return $this;
        $this->value = $value;
        $this->length = strlen($value);
    }

    public function encode()
    {
        return pack('a'.($this->length), $this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        if($length===NULL) throw new \Exception("Decoding octet string requires a length parameter.");
        $this->value = (unpack('a'.($length), $binary, $offset))[1];
        $this->length = strlen($this->value);
        return $this;
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function getLength(): int
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
        return (string)$this->value;
    }
}