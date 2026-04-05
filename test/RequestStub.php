<?php
declare(strict_types=1);

class RequestStub
{
    public static array $last = [];

    public static function send(string $printerURI, string $encodedPayload, ?string $user = null, ?string $password = null, array $curlOptions = []): \obray\ipp\transport\IPPPayload
    {
        self::$last = [
            'printerURI' => $printerURI,
            'encodedPayload' => $encodedPayload,
            'user' => $user,
            'password' => $password,
            'curlOptions' => $curlOptions,
        ];
        return new \obray\ipp\transport\IPPPayload();
    }
}
