<?php 

namespace obray\ipp;

class Request
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

    static public function send(string $printerURI, string $encodedPayload, string $user=null, string $password=null)
    {
        // interpret ipp request into http request
        $results = parse_url($printerURI);
        $postURL = $printerURI;
        if($results['scheme'] == 'ipp'){
            $postURL = 'http://' . $results['host'] . ':631' . $results['path'];
        }

        // setup headers
        $headers = array(0 => "Content-Type: application/ipp");
        if(!empty($user) && !empty($password)){
            $headers[] = "Authorization: Basic " . base64_encode($user.':'.$password);
        }

        // create curl request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedPayload);
        // execute the curl request and receive response
        $lastResponse = curl_exec($ch);
        $info = curl_getinfo($ch);
        if($info['http_code'] === 401) throw new \obray\ipp\exceptions\AuthenticationError();
        if($info['http_code'] !== 200) throw new \obray\ipp\exceptions\HTTPError($info['http_code']);
        
        // parse the response
        $responsePayload = new \obray\ipp\transport\IPPPayload();
        $responsePayload->decode($lastResponse);
        return $responsePayload;
    }
}