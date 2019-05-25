<?php
namespace obray\ipp\types;

class DateTime extends \obray\ipp\types\basic\OctetString implements \JsonSerializable
{
    protected $valueTag = 0x31;
    private $datetime;
    private $length = 11;

    public function __construct(string $dateTimeString=NULL)
    {
        if(!empty($dateTimeString)){
            $this->datetime = new \DateTime($dateTimeString);
        }
    }

    public function encode()
    {
        
        $diff = $this->datetime->format('O');
        $utcDiffDirection = $diff[0];
        $utcDiffHours = intval($diff[1] . $diff[2]);
        $utcDiffMins = intval($diff[3] . $diff[4]);
        $utcYear = intval($this->datetime->format('Y'));
        $utcMonth = intval($this->datetime->format('m'));
        $utcDay = intval($this->datetime->format('d'));
        $utcHours = intval($this->datetime->format('H'));
        $utcMins = intval($this->datetime->format('i'));
        $utcSecs = intval($this->datetime->format('s'));
        $utcDSec = round(intval($this->datetime->format('v'))/100);
        return  pack('n', $utcYear) .
                pack('c', $utcMonth) . 
                pack('c', $utcDay) .
                pack('c', $utcHours) .
                pack('c', $utcMins) .
                pack('c', $utcSecs) .
                pack('c', $utcDSec) . 
                pack('c', ord($utcDiffDirection)) .
                pack('c', $utcDiffHours) .
                pack('c', $utcDiffMins)
                ;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getValue()
    {
        return $this->datetime->format("Y-m-d H:i:s.vO");
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $datetime = unpack("nYear/cMonth/cDay/cHour/cMinute/cSecond/cSecFrac/cUtcDiffDirection/cUtcDiffHours/cUtcDiffMins", $binary, $offset);
        $datetime['UtcDiffDirection'] = chr($datetime['UtcDiffDirection']);
        $this->value = $datetime['Year'].'-'.str_pad($datetime['Month'],2,'0',STR_PAD_LEFT).'-'.str_pad($datetime['Day'],2,'0',STR_PAD_LEFT).' '.str_pad($datetime['Hour'],2,'0',STR_PAD_LEFT).':'.str_pad($datetime['Minute'],2,'0',STR_PAD_LEFT).':'.str_pad($datetime['Second'],2,'0',STR_PAD_LEFT).'.'.$datetime['SecFrac'].$datetime['UtcDiffDirection'].str_pad($datetime['UtcDiffHours'],2,'0',STR_PAD_LEFT).str_pad($datetime['UtcDiffMins'],2,'0',STR_PAD_LEFT);
        $this->datetime = new \DateTime($this->value);
        return $this;
    }

    public function __toString()
    {
        return $this->datetime->format("Y-m-d H:i:s.vO");
    }

    public function jsonSerialize()
    {
        return $this->datetime->format("Y-m-d H:i:s.vO");;
    }

}