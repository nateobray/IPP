<?php
namespace obray\ipp\types;

class URI extends USASCIIString implements \obray\ipp\interfaces\TypeInterface
{
    protected $valueTag = 0x45;
}