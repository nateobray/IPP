<?php
namespace obray\ipp\interfaces;

interface RequestInterface
{
    public static function send(
        string $printerURI,
        string $encodedPayload,
        ?string $user = null,
        ?string $password = null,
        array $curlOptions = []
    ): \obray\ipp\transport\IPPPayload;
}
