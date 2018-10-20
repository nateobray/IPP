<?php

namespace obray\enums;

class Resolution
{
    public $width;
    public $height;

    public function __construct($value)
    {
        $tmp = explode('x',$value);
        if(count($tmp) !== 2){
            throw new \Exception("Resolution is invalid.");
        }
        $this->width = $tmp[0];
        $this->height = $tmp[1];
    }
}