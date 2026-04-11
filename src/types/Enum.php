<?php
namespace obray\ipp\types;

class Enum extends \obray\ipp\types\basic\SignedInteger implements \JsonSerializable
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
                // Unknown/vendor-specific value — store raw and display as hex
                $this->value = $code;
                $this->key = null;
            } else {
                $this->key = $key;
                $this->value = $code;
            }
        }
    }

    public function decode($binary, $offset=0, $length=NULL)
    {    
        $this->value = \obray\ipp\transport\DecodeGuard::unpack('Nvalue', $binary, $offset, 4, 'enum value')['value'];
        $constants = $this->getConstants();
        $this->key = array_search($this->value, $constants);
        return $this;
    }

    public function __toString()
    {
        if (!is_string($this->key)) {
            return sprintf('0x%04X', (int) $this->value);
        }
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        if (!is_string($this->key)) {
            return sprintf('0x%04X', (int) $this->value);
        }
        return str_replace('_','-',strtolower($this->key));
    }
}
