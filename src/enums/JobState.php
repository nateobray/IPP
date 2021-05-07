<?php
namespace obray\ipp\enums;

class JobState extends \obray\ipp\types\Enum
{
    const PENDING = 3;
    const PENDINGHELD = 4;
    const PROCESSING = 5;
    const PROCESSINGSTOPPED = 6;
    const CANCELED = 7;
    const ABORTED = 8;
    const COMPLETED = 9;
}