<?php
namespace obray\ipp\types;

class Resolution
{
    private $crossFeedDirectionResolution;
    private $feedDirectionResolution;
    private $units;

    public function __construct(int $crossFeedDirectionResolution,int $feedDirectionResolution, string $units)
    {
        if(strlen($units)>1){
            throw new \Exception("Invalid units specified.");
        }
        if(!is_int($crossFeedDirectionResolution)){
            throw new \Exception("Cross feed direction resolution parameter invalid.");
        }
        if(!is_int($feedDirectionResolution)){
            throw new \Exception("Feed direction resolution parameter invalid.");
        }
        $this->crossFeedDirectionResolution = new \obray\ipp\types\basic\SignedInteger($crossFeedDirectionResolution);
        $this->feedDirectionResolution = new \obray\ipp\types\basic\SignedInteger($feedDirectionResolution);
        $this->units = new \obray\ipp\types\basic\SignedByte($units);
    }

    public function encode()
    {
        return $this->crossFeedDirectionResolution->encode() . $this->feedDirectionResolution->encode() . $this->units->encode();
    }
}