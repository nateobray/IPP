<?php
namespace obray\enums;

class JobState extends SplEnum
{
    const pending = 3;
    const pendingHeld = 4;
    const processing = 5;
    const processingStopped = 6;
    const canceled = 7;
    const aborted = 8;
    const completed = 9;
}