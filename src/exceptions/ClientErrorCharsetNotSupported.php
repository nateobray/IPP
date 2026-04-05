<?php
namespace obray\ipp\exceptions;

class ClientErrorCharsetNotSupported extends \Exception
{
    public function __construct($message="Client Error: Charset not supported.", $code=500, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
