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
        if($results['scheme'] == 'ipp'){
            $postURL = 'http://' . $results['host'] . ':631' . $results['path']??'';
        }

        // setup headers
        $headers = array(0 => "Content-Type: application/ipp");
        if(!empty($user) && !empty($password)){
            $headers[] = "Authorization: Basic " . base64_encode($user.':'.$password);
        }

        // setup headers
        $headers = array("Content-Type" => "application/ipp");
        $headers['Content-Length'] = strlen($encodedPayload);
        $headers['Connection'] = 'close';
        if(!empty($user) && !empty($password)){
            $headers["Authorization"] = "Basic " . base64_encode($user.':'.$password);
        }

        $response = \obray\HTTPClient::post($postURL, $encodedPayload, $headers);
        if($response->getStatusCode() == 401) throw new \obray\ipp\exceptions\AuthenticationError();
        if($response->getStatusCode() !== 200) throw new \obray\ipp\exceptions\HTTPError($response->getStatusCode());
        
        // parse the response
        $responsePayload = new \obray\ipp\transport\IPPPayload();
        $responsePayload->decode($response->getBody()->encode());
        
        return $responsePayload;
    }
}