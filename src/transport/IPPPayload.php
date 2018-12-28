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
        \obray\ipp\types\VersionNumber $versionNumber = NULL,
        \obray\ipp\types\Operation $operation = NULL,
        \obray\ipp\types\Integer $requestId = NULL,
        \obray\ipp\types\OctetString $document = NULL,
        \obray\ipp\OperationAttributes $operationAttributes = NULL,
        \obray\ipp\JobTemplateAttributes $jobTemplateAttributes = NULL,
        \obray\ipp\JobDescriptionAttributes $jobDescriptionAttributes = NULL,
        \obray\ipp\PrinterDescriptionAttributes $printerDescriptionAttributes = NULL,
        \obray\ipp\UnsupportedAttributes $unsupportedAttributes = NULL)
    {
        $this->versionNumber = $versionNumber;
        $this->operation = $operation;
        $this->requestId = $requestId;
        $this->document = $document;
        $this->operationAttributes = $operationAttributes;
        $this->jobTemplateAttributes = $jobTemplateAttributes;
        $this->jobDescriptionAttributes = $jobDescriptionAttributes;
        $this->printerDescriptionAttributes = $printerDescriptionAttributes;
        $this->unsupportedAttributes = $unsupportedAttributes;
    }

    public function encode()
    {
        $binary = $this->versionNumber->encode();
        $binary .= $this->operation->encode();
        $binary .= $this->requestId->encode();
        $binary .= $this->operationAttributes->encode();
        $binary .= pack('c',0x03); // end-of-attributes-tag
        if(!empty($this->document)){
            $binary .= $this->document->encode();
        }
        return $binary;
    }

    public function decode($binary)
    {
        
        print_r("\n------------------------------------\n");
        print_r("Decoding IPP Payload");
        print_r("\n------------------------------------\n\n");
        
        $unpacked = unpack("cMajor/cMinor/nStatusCode/lRequestID", $binary);
        print_r($unpacked);
        
        $this->versionNumber = new \obray\ipp\types\VersionNumber($unpacked['Major'] . '.' . $unpacked['Minor']);
        $this->operation = new \obray\ipp\types\StatusCode($unpacked['StatusCode']);
		
        $this->operationAttributes = new \obray\ipp\OperationAttributes();
		
        $this->operationAttributes->decode($binary, 8);
		exit();
    }

}