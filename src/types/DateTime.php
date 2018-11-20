<?php
namespace obray\types;

class DateTime extends \obray\ipp\types\basic\OctetString
{
    protected $valueTag = 0x31;
    private $unixTimeStamp;

    public function __construct(string $dateTimeString)
    {
        $this->unixTimeStamp = strtotime($dateTimeString);
    }

    public function encode()
    {
        $diff = date('O',$this->unixTimeStamp);
        $utcDiffDirection = $diff[0];
        $utcDiffHours = int($diff[1] . $dif[2]);
        $utcDiffMins = int($diff[3] . $dif[4]);

        return  unpack('s', date('Y',$this->unixTimeStamp)) . 
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

}