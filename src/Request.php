<?php 

namespace obray\ipp;

class Request implements \obray\ipp\interfaces\RequestInterface
{
    public const DEFAULT_CONNECT_TIMEOUT = 10;
    public const DEFAULT_TIMEOUT = 120;

    public static function sendRaw(
        string $printerURI,
        string $encodedPayload,
        ?string $user = null,
        ?string $password = null,
        array $curlOptions = []
    ): array {
        $postURL = self::buildPostUrl($printerURI);

        $headers = [
            0 => 'Content-Type: application/ipp',
            1 => 'Content-Length: ' . strlen($encodedPayload),
            2 => 'Connection: close',
        ];
        if (!empty($user) && !empty($password)) {
            $headers[] = 'Authorization: Basic ' . base64_encode($user . ':' . $password);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $postURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedPayload);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);

        foreach ($curlOptions as $curlOption) {
            if (!isset($curlOption['key']) || !isset($curlOption['value'])) {
                continue;
            }

            curl_setopt($ch, $curlOption['key'], $curlOption['value']);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverOutput = curl_exec($ch);
        $curlErrorCode = curl_errno($ch);
        $curlError = $curlErrorCode ? curl_error($ch) : null;
        $info = curl_getinfo($ch);

        curl_close($ch);

        if ($curlError !== null) {
            throw new \obray\ipp\exceptions\NetworkError($printerURI, $curlErrorCode, $curlError);
        }

        return [
            'body' => $serverOutput,
            'headers' => $headers,
            'http_info' => $info,
            'post_url' => $postURL,
        ];
    }

    /**
     * send
     * 
     * This method applies request headers, formulates the request and then
     * parses the response into a response payload.
     * 
     * @param string $encodedPayload This is the actual payload of the request
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    static public function send(string $printerURI, string $encodedPayload, ?string $user = null, ?string $password = null, array $curlOptions = []): \obray\ipp\transport\IPPPayload
    {
        $rawResponse = self::sendRaw($printerURI, $encodedPayload, $user, $password, $curlOptions);
        $server_output = $rawResponse['body'];
        $info = $rawResponse['http_info'];

        if($info['http_code'] == 401) throw new \obray\ipp\exceptions\AuthenticationError();
        if($info['http_code'] != 200) throw new \obray\ipp\exceptions\HTTPError($info['http_code']);

        try {
            $responsePayload = new \obray\ipp\transport\IPPPayload();
            $responsePayload->decode($server_output);
        } catch (\Throwable $exception) {
            throw new \obray\ipp\exceptions\IPPDecodeError($printerURI, $exception);
        }

        if ($responsePayload->statusCode->getValue() >= 0x0100) {
            throw new \obray\ipp\exceptions\IppStatusException($printerURI, $responsePayload);
        }

        return $responsePayload;
        
    }

    private static function buildPostUrl(string $printerURI): string
    {
        $results = parse_url($printerURI);
        if (!is_array($results) || !in_array($results['scheme'] ?? null, ['ipp', 'ipps'], true)) {
            return $printerURI;
        }

        // RFC 8010 §4.2: both IPP schemes use port 631 by default.
        $scheme = $results['scheme'] === 'ipps' ? 'https' : 'http';
        $postURL = $scheme . '://' . $results['host'] . ':' . ($results['port'] ?? 631)
            . ($results['path'] ?? '/');
        if (isset($results['query'])) {
            $postURL .= '?' . $results['query'];
        }
        return $postURL;
    }
}
