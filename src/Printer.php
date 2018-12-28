<?php 

namespace obray\ipp;

class Printer
{
    private $ipp;
    private $printerURI;
    private $user;

    public function __construct($uri, $user)
    {
        $this->printerURI = $uri;
        $this->user = $user;
    }

    public function printJob($document, array $attributes=NULL)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->printerURI = $this->printerURI;
        $operationAttributes->requestingUserName = $this->user;
        if(!empty($attributes['documentFormat'])){
        	$operationAttributes->documentFormat = $attributes['documentFormat'];
        }

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::printJob),
            new \obray\ipp\types\Integer(123456),
            new \obray\ipp\types\OctetString($document),
            $operationAttributes
        );
        $encodedPayload = $payload->encode();

        // debug encoded payload
        $hex = bin2hex($encodedPayload);
        $hex = str_split($hex,4);
        //print_r($hex);

        $headers = array(
            "Content-Type" => "application/ipp",
            "Authorization" => 'Basic ' . base64_encode('nate:y3knights')
        );
		
        $http = new \obray\HTTP();
        print_r("Adding Request\n");
        $http->addRequest($this->printerURI, \obray\HTTP::POST, $encodedPayload, $headers);
        $requests = ($http->getRequests())[0];
        echo "\n--------------------------------\n";
        echo "Request\n";
        echo "--------------------------------\n\n";
        echo $requests;
        $response = ($http->send())[0];
        if($response->getStatusCode()>=200 && $response->getStatusCode()<300){
            echo "\n--------------------------------\n";
            echo "Response with HTTP 200\n";
            echo "--------------------------------\n\n";
            echo $response;
            $responsePayload = new \obray\ipp\transport\IPPPayload();
            $responsePayload->decode($response->getBody());
        } else {
            echo "\n--------------------------------\n";
            echo "Response with HTTP Error Code\n";
            echo "--------------------------------\n\n";
            echo $response;
            throw new \Exception("Error (".$response->getStatusCode().") " . $response->getStatusDescription(),$response->getStatusCode());
        }

    }

    public function printURI($document, \obray\OperationAttributes $operationAttributes, \obray\JobTemplateAttributes $jobTemplateAttributes=NULL)
    {

    }

    public function validateJob(\obray\OperationAttributes $operationAttributes, \obray\JobTemplateAttributes $jobTemplateAttributes=NULL)
    {

    }

    public function createJob(\obray\OperationAttributes $operationAttributes, \obray\JobTemplateAttributes $jobTemplateAttributes=NULL)
    {

    }

    public function getPrinterAttributes(\obray\OperationAttributes $operationAttributes)
    {

    }

    public function getJobs(\obray\OperationAttributes $operationAttributes)
    {

    }

    public function pausePrinter()
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->printerURI = $this->printerURI;
        $operationAttributes->requestingUserName = $this->user;

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::pausePrinter),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );

        $encodedPayload = $payload->encode();
        $headers = array(
            "Content-Type" => "application/ipp",
            "Authorization" => 'Basic ' . base64_encode('nate:y3knights')
        );

        $http = new \obray\HTTP();
        $http->addRequest($this->printerURI, \obray\HTTP::POST, $encodedPayload, $headers);
        $requests = ($http->getRequests())[0];
        echo "\n--------------------------------\n";
        echo "Request\n";
        echo "--------------------------------\n\n";
        echo $requests;
        $response = ($http->send())[0];
        if($response->getStatusCode()>=200 && $response->getStatusCode()<300){
            echo "\n--------------------------------\n";
            echo "Response with HTTP 200\n";
            echo "--------------------------------\n\n";
            echo $response;
            $responsePayload = new \obray\ipp\transport\IPPPayload();
            $responsePayload->decode($response->getBody());
        } else {
            echo "\n--------------------------------\n";
            echo "Response\n";
            echo "--------------------------------\n\n";
            echo $response;
            throw new \Exception("Error (".$response->getStatusCode().") " . $response->getStatusDescription(),$response->getStatusCode());
        }
    }

    public function resumePrinter(\obray\OperationAttributes $operationAttributes)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->printerURI = $this->printerURI;
        $operationAttributes->requestingUserName = $this->user;

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::resumePrinter),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );

        $encodedPayload = $payload->encode();
        $headers = array(
            "Content-Type" => "application/ipp",
            "Authorization" => 'Basic ' . base64_encode('nate:**')
        );

        $http = new \obray\HTTP();
        $http->addRequest("http://10.5.2.82:631/printers/devprinter", \obray\HTTP::POST, $encodedPayload, $headers);
        $requests = ($http->getRequests())[0];
        echo "\n--------------------------------\n";
        echo "Request\n";
        echo "--------------------------------\n\n";
        echo $requests;
        $response = ($http->send())[0];
        if($response->getStatusCode()>=200 && $response->getStatusCode()<300){
        
            echo "\n--------------------------------\n";
            echo "Response with HTTP 200\n";
            echo "--------------------------------\n\n";
            echo $response;
        } else {
            echo "\n--------------------------------\n";
            echo "Response\n";
            echo "--------------------------------\n\n";
            echo $response;
            throw new \Exception("Error (".$response->getStatusCode().") " . $response->getStatusDescription(),$response->getStatusCode());
        }
    }

    public function purgeJobs(\obray\OperationAttributes $operationAttributes)
    {

    }
}