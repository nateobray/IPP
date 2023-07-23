<?php
namespace obray\ipp\enums;

use obray\ipp\types\Enum;

class PrinterState extends Enum
{
     const idle = 3;
     const processing = 4;
     const stopped = 5;
}