<?php
namespace obray\enums

class StatusCodes extends SplEnum {
    const __default = self::successful-ok;
    // success codes
    const successful-ok = 0;
    const successful-ok-ignored-or-substituted-attributes = 1;
    const successful-ok-conflicting-attributes = 2;
    // error codes
    const client-error-bad-request = 0x0401;
    const May = 1025;
    const June = 6;
    const July = 7;
    const August = 8;
    const September = 9;
    const October = 10;
    const November = 11;
    const December = 12;
}