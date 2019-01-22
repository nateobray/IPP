<?php
namespace obray\ipp\types;

class URIScheme extends \obray\ipp\types\basic\USASCIIString implements \obray\ipp\interfaces\TypeInterface
{
    protected $valueTag = 0x46;
}