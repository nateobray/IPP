<?php

namespace obray\ipp\enums;

class JobHoldUntil extends \obray\ipp\types\Enum
{
    const no_hold = "no-hold";
    const indefinite = "indefinite";
    const day_time = "day-time";
    const evening = "evening";
    const night = "night";
    const weekend = "weekend";
    const second_shift = "second-shift";
    const third_shift = "third-shift";
}