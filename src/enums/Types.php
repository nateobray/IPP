<?php
namespace obray\ipp\enums;

class Types extends \obray\ipp\types\Enum
{
    const BOOLEAN = 0x22;
    const CHARSET = 0x47;
    const DATETIME = 0x31;
    const ENUM = 0x23;
    const INTEGER = 0x21;
    const KEYWORD = 0x44;
    const MIMEMEDIATYPE = 0x49;
    const NAME = 0x0008;
    const NATURALLANGUAGE = 0x0009;
    const OCTETSTRING = 0x000A;
    const RANGEOFINTEGER = 0x33;
    const RESOLUTION = 0x32;
    const STATUSCODE = 0x000D;
    const TEXT = 0x000E;
    const URI = 0x45;
    const URISCHEME = 0x46;
    const VERSIONNUMBER = 0x12;
}