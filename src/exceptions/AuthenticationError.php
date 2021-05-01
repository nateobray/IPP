<?php
namespace obray\ipp\exceptions;

class AuthenticationError extends \Exception
{
    public function __construct($message="Authentication error: make sure you are passing the correct credentials.", $code=401, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}