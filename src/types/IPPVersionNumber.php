<?php
namespace obray\types;

class IPPVersionNumber implements \obray\interfaces\IPPTypeInterface
{
    private $majorVersionNumber;
    private $minorVersionNumber;

    public function __constructor(string $versionString){
        $versionParts = explode(".",$versionString);
        if(count($versionParts) !== 2){
            throw new \Exception("Invalid version number.");
        }
        $this->majorVersionNumber = $versionParts[0];
        $this->minorversionNumber = $versionParts[1];
    }

    public function __toString()
    {
        return $this->majorVersionNumber . '.' . $this->minorVersionNumber;
    }

    public function encode()
    {

    }

    public function decode()
    {

    }
}