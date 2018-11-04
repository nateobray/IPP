<?php
namespace obray\ipp\enums;

class Types extends \obray\ipp\types\Enum
{
    const BOOLEAN = 0x0001;
    const CHARSET = 0x0002;
    const DATETIME = 0x0003;
    const ENUM = 0x0004;
    const INTEGER = 0x0005;
    const KEYWORD = 0x0006;
    const MIMEMEDIATYPE = 0x0007;
    const NAME = 0x0008;
    const NATURALLANGUAGE = 0x0009;
    const OCTETSTRING = 0x000A;
    const RANGEOFINTEGER = 0x000B;
    const RESOLUTION = 0x000C;
    const STATUSCODE = 0x000D;
    const TEXT = 0x000E;
    const URI = 0x000F;
    const URISCHEME = 0x0010;
    const VERSIONNUMBER = 0x0011;
}