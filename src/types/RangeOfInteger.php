<?php
namespace obray\ipp\types;

class RangeOfInteger implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $valueTag = 0x33;
    private $length = 8;
    private $lowerBound;
    private $upperBound;

    public function __construct(int $lowerBound=NULL, int $upperBound=NULL)
    {
        if($lowerBound===NULL || $upperBound===NULL) return $this;
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
        $this->upperBound = (new \obray\ipp\types\basic\SignedInteger())->decode($binary, $offset);
        return $this;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function getValue()
    {
        $range = $this->lowerBound;
        if(strlen($this->upperBound)!==0){
            $range .= '-' . $this->upperBound;
        }
        return $range;
    }

    public function __toString()
    {
        $range = $this->lowerBound;
        if(strlen($this->upperBound)!==0){
            $range .= '-' . $this->upperBound;
        }
        return $range;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $range = $this->lowerBound;
        if(strlen($this->upperBound)!==0){
            $range .= '-' . $this->upperBound;
        }
        return $range;
    }
}