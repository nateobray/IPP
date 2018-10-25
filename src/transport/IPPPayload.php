<?php
namespace obray\ipp\transport;

class IPPPayload
{
    private $versionNumber;
    private $operationId;
    private $requestId;
    private $statusCode;
    private $operationAttributes;
    private $jobTemplateAttributes;
    private $jobDescriptionAttributes;
    private $printerDescriptionAttributes;
    private $unsupportedAttributes;
    private $document;

    public function __construct(
        \obray\ipp\types\VersionNumber $versionNumber,
        \obray\ipp\types\Operation $operation,
        \obray\ipp\types\Integer $requestId,
        \obray\ipp\OperationAttributes $operationAttributes,
        \obray\ipp\JobTemplateAttributes $jobTemplateAttributes = NULL,
        \obray\ipp\JobDescriptionAttributes $jobDescriptionAttributes = NULL,
        \obray\ipp\PrinterDescriptionAttributes $printerDescriptionAttributes = NULL,
        \obray\ipp\UnsupportedAttributes $unsupportedAttributes = NULL)
    {
        $this->versionNumber = $versionNumber;
        $this->operation = $operation;
        $this->requestId = $requestId;
        $this->operationAttributes = $operationAttributes;
        $this->jobTemplateAttributes = $jobTemplateAttributes;
        $this->jobDescriptionAttributes = $jobDescriptionAttributes;
        $this->printerDescriptionAttributes = $printerDescriptionAttributes;
        $this->unsupportedAttributes = $unsupportedAttributes;
    }

    public function encode()
    {
        print_r("encoding binary...\n");
        $binary = $this->versionNumber->encode();
        $binary .= $this->operation->encode();
        $binary .= $this->requestId->encode();
        $binary .= $this->operationAttributes->encode();
        //$binary .= $this->request->encode()
        print_r("Binary String: ");
        print_r(unpack("cMajor/cMinor/lOperation/lRequestID",$binary));
        print_r("\n");
    }

    public function decode()
    {

    }

}