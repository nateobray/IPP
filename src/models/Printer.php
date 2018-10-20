<?php 

namespace obray;

class Printer
{
    private $ipp;

    public function __construct(\obray\transport\IPP $ipp)
    {
        $this->ipp = $ipp;
    }

    public function printJob($document, \obray\OperationAttributes $operationAttributes, \obray\JobTemplateAttributes $jobTemplateAttributes=NULL)
    {
        $payload = new \obray\transport\IPPPayload(
            1234,
            12345,
            $operationAttributes,
            $jobTemplateAttributes,
            $document
        );
        $data = new \obray\transport\IPPEncoder($payload);
        //$this->ipp->setPostData($data);
        //$this->ipp->send();
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

    public function pausePrinter(\obray\OperationAttributes $operationAttributes)
    {

    }

    public function resumePrinter(\obray\OperationAttributes $operationAttributes)
    {

    }

    public function purgeJobs(\obray\OperationAttributes $operationAttributes)
    {

    }
}