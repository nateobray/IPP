<?php
namespace obray\ipp\types;

class DateTime extends \obray\ipp\types\basic\OctetString implements \JsonSerializable
{
    protected $valueTag = 0x31;
    private $unixTimeStamp;

    public function __construct(string $dateTimeString=NULL)
    {
        if(!empty($dateTimeStamp)){
        $this->unixTimeStamp = strtotime($dateTimeString);
        }
    }

    public function encode()
    {
        $diff = date('O',$this->unixTimeStamp);
        $utcDiffDirection = $diff[0];
        $utcDiffHours = int($diff[1] . $dif[2]);
        $utcDiffMins = int($diff[3] . $dif[4]);

        return  unpack('n', date('Y',$this->unixTimeStamp)) . 
                unpack('c', date('m',$this->unixTimeStamp)) . 
                unpack('c', date('d',$this->unixTimeStamp)) . 
                unpack('c', date('h',$this->unixTimeStamp)) . 
                unpack('c', date('i',$this->unixTimeStamp)) . 
                unpack('c', date('d',$this->unixTimeStamp)) . 
                unpack('c', 0) . 
                unpack('c', $utcDiffDirection) . 
                unpack('c', $utcDiffHours) .
                unpack('c', $urcDiffMins);
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $datetime = unpack("nYear/cMonth/cDay/cHour/cMinute/cSecond/cSecFrac/a1UtcDiffDirection/cUtcDiffHours/cUtcDiffMins", $binary, $offset);
        $this->value = $datetime['Year'].'-'.str_pad($datetime['Month'],2,'0',STR_PAD_LEFT).'-'.str_pad($datetime['Day'],2,'0',STR_PAD_LEFT).' '.str_pad($datetime['Hour'],2,'0',STR_PAD_LEFT).':'.str_pad($datetime['Minute'],2,'0',STR_PAD_LEFT).':'.str_pad($datetime['Second'],2,'0',STR_PAD_LEFT).'.'.$datetime['SecFrac'].$datetime['UtcDiffDirection'].str_pad($datetime['UtcDiffHours'],2,'0',STR_PAD_LEFT).':'.str_pad($datetime['UtcDiffMins'],2,'0',STR_PAD_LEFT);
        $this->unixTimeStamp = strtotime($this->value);
    }

    public function jsonSerialize()
    {
        return $this->value;
    }

}