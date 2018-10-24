<?php
namespace obray\ipp\types;

class RangeOfInteger implements \obray\ipp\interfaces\IPPTypeInterface
{
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
}