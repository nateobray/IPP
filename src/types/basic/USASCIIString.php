<?php
namespace obray\ipp\types\basic;

class USASCIIString implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $value;
    protected $valueTag;
    private $length;

    public function __construct($value=NULL)
    {
        if($value===NULL) return $this;
        $this->length = strlen($value);
        $this->value = $value;
    }

    public function encode()
    {
        return pack('a'.($this->length), $this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        if ($length === NULL) {
            throw new \Exception("Decoding US-ASCII string requires a length parameter.");
        }

        \obray\ipp\transport\DecodeGuard::requireBytes($binary, $offset, $length, 'US-ASCII string');
        $this->value = \obray\ipp\transport\DecodeGuard::unpack(
            'a' . $length . 'value',
            $binary,
            $offset,
            $length,
            'US-ASCII string'
        )['value'];
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
        return (string)$this->value;
    }
}
