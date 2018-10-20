<?php
namespace obray\exceptions;

class ClientErrorCharsetNotSupported extends \Exception
{
    public function __construct($message="Client Error: Charset not supported.", $code=500, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}