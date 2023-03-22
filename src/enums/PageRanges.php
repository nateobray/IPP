<?php
namespace obray\ipp\enums;

class PageRanges
{
    public function __construct($value)
    {
        $this->parseRanges($value);
    }

    public function parseRanges($ranges)
    {
        $tmp = explode(",", $ranges??'');
        if(empty($tmp)){
            throw new \Exception("Invalid page range.");
        }
        forEach($tmp as $range){
            $newRange = $this->parseRange($range);
            if(!empty($previousRange)){
                if($previousRange["low"] > $newRange["low"] || $previousRange["high"] > $newRange["low"]){
                    throw new \Exception("Page ranges must be in order from lowest range to highest and cannot overlap");
                }
            }
            $this->ranges[] = $previousRange = $newRange;
        }
        
    }

    public function parseRange($range)
    {
        $tmp = explode("-", $range??'');
        if( count($tmp) !== 2 ){
            throw new \Exception("Invalid page range.");
        }
        $low = $tmp[0];
        $high = $tmp[1];
        if( !is_int($low) || !is_int($high) ){
            throw new \Exception("Invalid page range.  One or more page values is invalid");
        }
        if( $low > $high ){
            throw new \Exception("Invalid page range. Low page range value cannot exceed the high page range value.");
        }
        return array("low" => $low, "high" => $high);
    }

}