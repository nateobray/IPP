<?php

namespace obray\interfaces;

interface IPPTypeInterface{
    public function __toString();
    public function encode();
    public function decode();
}