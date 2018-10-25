<?php
namespace obray\ipp\types;

class Integer extends \obray\ipp\types\basic\SignedInteger
{
    protected $valueTag = 0x21;
}