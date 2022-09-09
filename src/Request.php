<?php 

namespace obray\ipp;

class Request implements \obray\ipp\interfaces\RequestInterface
{
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

    static public function send(string $printerURI, string $encodedPayload, string $user=null, string $password=null): \obray\ipp\transport\IPPPayload
    {
        // interpret ipp request into http request
        $results = parse_url($printerURI);
        $postURL = $printerURI;
        if(empty($results['path'])) $results['path'] = '';
        if($results['scheme'] == 'ipp'){
            $postURL = 'http://' . $results['host'] . ':631' . $results['path'];
        }

        // setup headers
        $headers = array(
            0 => "Content-Type: application/ipp",
            1 => "Content-Length: " . strlen($encodedPayload),
            2 => "Connection: close"
        );
        if(!empty($user) && !empty($password)){
            $headers[] = "Authorization: Basic " . base64_encode($user.':'.$password);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$postURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedPayload);

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);

        if(curl_errno($ch)) throw new \Exception(curl_error($ch));
        $info = curl_getinfo($ch);

        if($info['http_code'] == 401) throw new \obray\ipp\exceptions\AuthenticationError();
        if($info['http_code'] != 200) throw new \obray\ipp\exceptions\HTTPError('http_code');

        // Further processing ...
        $responsePayload = new \obray\ipp\transport\IPPPayload();
        $responsePayload->decode($server_output);
        return $responsePayload;
        
    }
}