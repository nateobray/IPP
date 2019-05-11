<?php
namespace obray\ipp\types;

class Resolution implements \obray\ipp\interfaces\TypeInterface, \JsonSerializable
{
    protected $valueTag = 0x32;
    private $crossFeedDirectionResolution;
    private $feedDirectionResolution;
    private $units;

    public function __construct(int $crossFeedDirectionResolution=NULL, int $feedDirectionResolution=NULL, int $units=NULL)
    {
        if($crossFeedDirectionResolution===NULL || $feedDirectionResolution===NULL || $units===NULL) return $this;
        $this->crossFeedDirectionResolution = new \obray\ipp\types\basic\SignedInteger($crossFeedDirectionResolution);
        $this->feedDirectionResolution = new \obray\ipp\types\basic\SignedInteger($feedDirectionResolution);
        $this->units = new \obray\ipp\types\basic\SignedByte($units);
    }

    public function encode()
    {
        return $this->crossFeedDirectionResolution->encode() . $this->feedDirectionResolution->encode() . $this->units->encode();
    }

    public function decode($binary, $offset=0, $length=NULL){
        
        $this->crossFeedDirectionResolution = (new \obray\ipp\types\basic\SignedInteger())->decode($binary, $offset);
        $offset += $this->crossFeedDirectionResolution->getLength();
        $this->feedDirectionResolution = (new \obray\ipp\types\basic\SignedInteger())->decode($binary, $offset);
        $offset += $this->feedDirectionResolution->getLength();
        $this->units = (new \obray\ipp\types\basic\SignedByte())->decode($binary, $offset);
        return $this;
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function __toString()
    {
        $units = '';
        if( $this->units->getValue() == 3 ){ $units = 'dpi'; }
        if( $this->units->getValue() == 4 ){ $units = 'dpc'; }
        return $this->crossFeedDirectionResolution . 'x' . $this->feedDirectionResolution . ' ' . $units;
    }

    public function jsonSerialize()
    {
        return (string)$this;
    }
}