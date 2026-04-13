<?php
namespace obray\ipp\enums;

class DocumentState extends \obray\ipp\types\Enum
{
    const pending            = 3;
    const processing         = 5;
    const processing_stopped = 6;
    const canceled           = 7;
    const aborted            = 8;
    const completed          = 9;
}
