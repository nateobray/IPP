<?php
namespace obray\ipp\types\basic;

class USASCIIString implements \obray\ipp\interfaces\TypeInterface
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
        $binary = '';
        forEach(str_split($this->value) as $char){
            $binary .= pack('c',$char);
        }
        return $binary;
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
}