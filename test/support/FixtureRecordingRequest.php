<?php
declare(strict_types=1);

namespace obray\ipp\test\support;

final class FixtureRecordingRequest implements \obray\ipp\interfaces\RequestInterface
{
    public static array $lastExchange = [];

    public static function reset(): void
    {
        self::$lastExchange = [];
    }

    public static function send(
        string $printerURI,
        string $encodedPayload,
        ?string $user = null,
        ?string $password = null,
        array $curlOptions = []
    ): \obray\ipp\transport\IPPPayload {
        $rawResponse = \obray\ipp\Request::sendRaw($printerURI, $encodedPayload, $user, $password, $curlOptions);

        $httpCode = $rawResponse['http_info']['http_code'] ?? 0;
        if ($httpCode === 401) {
            throw new \obray\ipp\exceptions\AuthenticationError();
        }
        if ($httpCode !== 200) {
            throw new \obray\ipp\exceptions\HTTPError($httpCode);
        }

        try {
            $responsePayload = new \obray\ipp\transport\IPPPayload();
            $responsePayload->decode((string) $rawResponse['body']);
        } catch (\Throwable $exception) {
            throw new \obray\ipp\exceptions\IPPDecodeError($printerURI, $exception);
        }

        self::$lastExchange = [
            'printer_uri' => $printerURI,
            'encoded_request' => $encodedPayload,
            'raw_response' => (string) $rawResponse['body'],
            'http_info' => $rawResponse['http_info'],
            'post_url' => $rawResponse['post_url'],
            'headers' => $rawResponse['headers'],
            'user' => $user,
        ];

        return $responsePayload;
    }
}
