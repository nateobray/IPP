<?php
namespace obray\ipp\types;

class URIScheme extends USASCIIString implements \obray\ipp\interfaces\TypeInterface
{
    protected $valueTag = 0x46;
}