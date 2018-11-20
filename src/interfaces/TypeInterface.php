<?php

namespace obray\ipp\interfaces;

interface TypeInterface
{
    public function getValueTag();
    public function encode();
    public function decode($binary, $offset=0, $length=NULL);
}