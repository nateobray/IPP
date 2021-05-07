<?php
namespace obray\ipp\interfaces;

interface RequestInterface
{
    public static function send(string $printerURI, string $encodedPayload, string $user=null, string $password=null): \obray\ipp\transport\IPPPayload;
}