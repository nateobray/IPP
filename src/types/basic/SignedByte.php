<?php
namespace obray\ipp\types\basic;

class SignedByte implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $value;
    protected $valueTag;
    private $type;
    private $length = 1;

    public function __construct($value=NULL)
    {
        if($value===NULL) return $this;
        if(in_array(getType($value), ['integer', 'boolean'])){
            $this->value = (int)$value;
        } else {
            $this->value = (int)ord($value);
        }
        if($this->value > 255) throw new \Exception("Invalid Signed Byte");
    }

    public function encode()
    {
        return pack('c', $this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $this->value = unpack('c', $binary, $offset)[1];
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