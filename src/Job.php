<?php
namespace obray\ipp;

class Job
{
    private $ipp;
    private $printerURI;
    private $jobID;
    private $user;
    private $password;
    
    private $lastRequest;
    private $lastResponse;

    public function __construct($uri, $jobID, $user, $password=NULL)
    {
        $this->printerURI = $uri;
        $this->jobID = $jobID;
        $this->user = $user;
        $this->password = $password;
    }

    public function sendDocument()
    {

    }

    public function sendURI()
    {

    }

    public function cancelJob()
    {

    }

    public function getJobAttributes()
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'job-id'} = $this->jobID;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::getJobAttributes),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    public function holdJob()
    {

    }

    public function releaseJob()
    {

    }

    public function restartJob()
    {

    }

    /**
     * send
     * 
     * This method applies request headers, formulates the request and then
     * parses the response into a response payload.
     * 
     * @params string $encodedPayload This is the actual payload of the request
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    private function send(string $encodedPayload)
    {
        $headers = array("Content-Type" => "application/ipp");
        if(!empty($this->user) && !empty($this->password)){
            $headers["Authorization"] = 'Basic ' . base64_encode($this->user.':'.$this->password);
        }

        $http = new \obray\HTTP();
        $http->addRequest($this->printerURI, \obray\HTTP::POST, $encodedPayload, $headers);
        $this->lastRequest = ($http->getRequests())[0];
        $this->lastResponse = ($http->send())[0];
        
        $responsePayload = new \obray\ipp\transport\IPPPayload();
        $responsePayload->decode($this->lastResponse->getBody());
        return $responsePayload;
    }
}