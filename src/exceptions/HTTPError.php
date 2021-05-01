<?php
namespace obray\ipp\exceptions;

class HTTPError extends \Exception
{
    public function __construct($code, $message="HTTP Error code: the request return an http status code", Exception $previous = null) {
        $message = $message .' (' . $code . ')';
        parent::__construct($message, $code, $previous);
    }
}