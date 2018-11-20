<?php
namespace obray\ipp\types\basic;

class LocalizedString implements \obray\ipp\interfaces\TypeInterface
{
    protected $value;
    protected $valueTag;
    private $length;

    public function __construct($value=NULL, $length=NULL)
    {
        
        if($value===NULL) return $this;
        $this->value = $value;
        $this->length = strlen($value);
        if($length!==NULL){
            $this->length = $length;
        }
        return $this;
    }

    public function encode()
    {
        return pack('a'.($this->length), $this->value);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $this->length = $length;
        if($length===NULL) throw new \Exception("Decoding localized string requires a length parameter.");
        $this->value = (unpack('a'.$length, $binary, $offset))[1];
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
}