<?php

namespace obray\ipp\test;

class FakeRequest implements \obray\ipp\interfaces\RequestInterface
{
    public static array $lastCall = [];
    public static ?\obray\ipp\transport\IPPPayload $nextResponse = null;

    public static function reset(): void
    {
        self::$lastCall = [];
        self::$nextResponse = null;
    }

    public static function send(
        string $printerURI,
        string $encodedPayload,
        ?string $user = null,
        ?string $password = null,
        array $curlOptions = []
    ): \obray\ipp\transport\IPPPayload {
        $header = unpack('Cmajor/Cminor/noperation/NrequestId', $encodedPayload);
        $offset = 8;

        $operationAttributes = new \obray\ipp\OperationAttributes();
        $nextTag = $operationAttributes->decode($encodedPayload, $offset);

        $jobAttributes = null;
        if ($nextTag === 0x02) {
            $jobAttributes = new \obray\ipp\JobAttributes();
            $nextTag = $jobAttributes->decode($encodedPayload, $offset);
        }

        $printerAttributes = null;
        if ($nextTag === 0x04) {
            $printerAttributes = new \obray\ipp\PrinterAttributes();
            $nextTag = $printerAttributes->decode($encodedPayload, $offset);
        }

        $subscriptionAttributes = null;
        if ($nextTag === 0x06) {
            $subscriptionAttributes = new \obray\ipp\SubscriptionAttributes();
            $nextTag = $subscriptionAttributes->decode($encodedPayload, $offset);
        }

        $document = '';
        if (isset($encodedPayload[$offset]) && ord($encodedPayload[$offset]) === 0x03) {
            $offset++;
            $document = substr($encodedPayload, $offset);
        }

        self::$lastCall = [
            'printerURI' => $printerURI,
            'user' => $user,
            'password' => $password,
            'curlOptions' => $curlOptions,
            'version' => $header['major'] . '.' . $header['minor'],
            'operation' => $header['operation'],
            'requestId' => $header['requestId'],
            'operationAttributes' => $operationAttributes,
            'jobAttributes' => $jobAttributes,
            'printerAttributes' => $printerAttributes,
            'subscriptionAttributes' => $subscriptionAttributes,
            'document' => $document,
        ];

        if (self::$nextResponse !== null) {
            $response = self::$nextResponse;
            self::$nextResponse = null;
            return $response;
        }

        $response = new \obray\ipp\transport\IPPPayload();
        $response->versionNumber = new \obray\ipp\types\VersionNumber(self::$lastCall['version']);
        $response->requestId = new \obray\ipp\types\Integer(self::$lastCall['requestId']);
        $response->statusCode = new \obray\ipp\types\StatusCode(\obray\ipp\types\StatusCode::successful_ok);
        $response->operationAttributes = new \obray\ipp\OperationAttributes();

        return $response;
    }
}
