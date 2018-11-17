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

    public function printJob($document)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->printerURI = $this->printerURI;
        $operationAttributes->requestingUserName = $this->user;

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::printJob),
            new \obray\ipp\types\Integer(123456),
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        $headers = array(
            "Content-Type" => "application/ipp",
            "Authorization" => 'Basic ' . base64_encode('nate:y3k$$w0rd')
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
            
        } else {
            echo "\n--------------------------------\n";
            echo "Response\n";
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
            $operationAttributes
        );

        $encodedPayload = $payload->encode();
        $headers = array(
            "Content-Type" => "application/ipp",
            "Authorization" => 'Basic ' . base64_encode('nate:y3k$$w0rd')
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

    }

    public function purgeJobs(\obray\OperationAttributes $operationAttributes)
    {

    }
}