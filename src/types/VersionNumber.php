<?php
namespace obray\ipp\types;

class VersionNumber implements \obray\ipp\interfaces\TypeInterface
{
    private $valueTag = 0x12;
    private $majorVersionNumber;
    private $minorVersionNumber;

    public function __construct(string $versionString){
        $versionParts = explode(".",$versionString);
        if(count($versionParts) !== 2){
            throw new \Exception("Invalid version number.");
        }
        $this->majorVersionNumber = (int)$versionParts[0];
        $this->minorVersionNumber = (int)$versionParts[1];
    }

    public function __toString()
    {
        return $this->majorVersionNumber . '.' . $this->minorVersionNumber;
    }

    public function getValueTag()
    {
        return $valueTag;
    }

    public function encode()
    {
        return pack('c',$this->majorVersionNumber) . pack('c',$this->minorVersionNumber);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {

    }
}