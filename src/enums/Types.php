<?php
namespace obray\ipp\enums;

class Types extends \obray\ipp\types\Enum
{
    const UNSUPPORTED = 0x10;
    const UNKNOWN = 0x12;
    const NOVALUE = 0x13;
    const BOOLEAN = 0x22;
    const CHARSET = 0x47;
    const DATETIME = 0x31;
    const ENUM = 0x23;
    const INTEGER = 0x21;
    const KEYWORD = 0x44;
    const MIMEMEDIATYPE = 0x49;
    const NAME = 0x0008;
    const NATURALLANGUAGE = 0x48;
    const OCTETSTRING = 0x30;
    const RANGEOFINTEGER = 0x33;
    const TEXTWITHLANGUAGE = 0x35;
    const NAMEWITHLANGUAGE = 0x36;
    const TEXTWITHOUTLANGUAGE = 0x41;
    const NAMEWITHOUTLANGUAGE = 0x42;
    const RESOLUTION = 0x32;
    const STATUSCODE = 0x000D;
    const TEXT = 0x000E;
    const URI = 0x45;
    const URISCHEME = 0x46;
    const NOVAL = 0x13;
}