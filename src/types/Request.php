<?php
namespace obray\ipp\types;

class Request implements \obray\ipp\interfaces\IPPTypeInterface
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function encode()
    {
        print_r('Encoding Request ID: '.$this."\n");
        return pack('i', $this->id);
    }

    public function decode()
    {

    }
}