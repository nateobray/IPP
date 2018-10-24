<?php 

namespace obray\ipp;

class Printer
{
    private $ipp;

    public function printJob($document, \obray\ipp\OperationAttributes $operationAttributes, \obray\ipp\JobTemplateAttributes $jobTemplateAttributes=NULL)
    {
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::printJob),
            new \obray\ipp\types\Integer(123456),
            $operationAttributes,
            $jobTemplateAttributes
        );
        $payload->encode();
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