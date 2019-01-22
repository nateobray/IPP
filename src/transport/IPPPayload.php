<?php
namespace obray\ipp\transport;

class IPPPayload
{
    public $versionNumber;
    private $operation;
    public $requestId;
    public $statusCode;
    public $operationAttributes;
    private $jobTemplateAttributes;
    public $jobDescriptionAttributes;
    public $printerDescriptionAttributes;
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
        $unpacked = unpack("cMajor/cMinor/nStatusCode/lRequestID", $binary);
        
        $this->versionNumber = new \obray\ipp\types\VersionNumber($unpacked['Major'] . '.' . $unpacked['Minor']);
        $this->statusCode = new \obray\ipp\types\StatusCode($unpacked['StatusCode']);
        $this->requestId = new \obray\ipp\types\Integer($unpacked['RequestID']);
        
        $offset = 8;
        
        $this->operationAttributes = new \obray\ipp\OperationAttributes();
        $newTag = $this->operationAttributes->decode($binary, $offset);
        
        if($newTag!==false && $newTag === 0x02){
            $this->jobDescriptionAttributes = array();
            while(true){
                $this->jobDescriptionAttributes[] = new \obray\ipp\JobDescriptionAttributes();
                $newTag = $this->jobDescriptionAttributes[count($this->jobDescriptionAttributes)-1]->decode($binary, $offset);
                if( $newTag === false || $newTag !== 0x02 ){
                    break;
                }
            }
            if(count($this->jobDescriptionAttributes) === 1){
                $this->jobDescriptionAttributes = $this->jobDescriptionAttributes[0];
            }
        }

        
        if($newTag!==false && $newTag === 0x04){
            $this->printerDescriptionAttributes = new \obray\ipp\PrinterDescriptionAttributes();
            $newTag = $this->printerDescriptionAttributes->decode($binary, $offset);
        }

        


        
    }

}