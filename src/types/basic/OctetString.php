<?php
namespace obray\ipp\types\basic;

class OctetString
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function encode()
    {
        $binary;
        forEach(\str_split($this->value) as $char){
            $binary .= \unpack('c',$char);
        }
        return $binary;
    }

    public function decode()
    {
        
    }
}