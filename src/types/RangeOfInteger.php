<?php
namespace obray\ipp\types;

class RangeOfInteger implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $valueTag = 0x33;
    private $lowerBound;
    private $upperBound;

    public function __construct(int $lowerBound, int $upperBound)
    {
        $this->lowerBound = new \obray\ipp\types\basic\SignedInteger($lowerBound);
        $this->upperBound = new \obray\ipp\types\basic\SignedInteger($upperBound);
    }

    public function encode()
    {
        return $this->lowerBound->encode() . $this->upperBound->encode();
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $this->lowerBound = (new \obray\ipp\types\basic\SignedInteger())->decode($binary, $offset);
        $offset += $this->lowerBound->getLength();
        $this->upperBound = (new \obray\ipp\types\basic\SignedInteger());
        return $this;
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function jsonSerialize()
    {
        $range = $this->lowerBound;
        if(strlen($this->upperBound)!==0){
            $range .= '-' . $this->upperBound;
        }
        return $range;
    }
}