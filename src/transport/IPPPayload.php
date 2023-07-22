<?php
namespace obray\ipp\transport;

/**
 * IPP Payload
 * 
 * This class is responsible for encoding and decoded the payload data that will
 * be submitted in an IPP request.  It confirms to RFC8010 section 3.1.
 * 
*/

class IPPPayload
{
    public $versionNumber;
    private $operation;
    public $requestId;
    public $statusCode;
    public $operationAttributes;
    public $jobAttributes;
    public $printerAttributes;
    private $unsupportedAttributes;
    private $document;

    public function __construct(
        \obray\ipp\types\VersionNumber $versionNumber = NULL,
        \obray\ipp\types\Operation $operation = NULL,
        \obray\ipp\types\Integer $requestId = NULL,
        \obray\ipp\types\OctetString $document = NULL,
        \obray\ipp\OperationAttributes $operationAttributes = NULL,
        \obray\ipp\JobAttributes $jobAttributes = NULL,
        \obray\ipp\PrinterAttributes $printerAttributes = NULL,
        \obray\ipp\UnsupportedAttributes $unsupportedAttributes = NULL)
    {
        $this->versionNumber = $versionNumber;
        $this->operation = $operation;
        $this->requestId = $requestId;
        $this->document = $document;
        $this->operationAttributes = $operationAttributes;
        $this->jobAttributes = $jobAttributes;
        $this->printerAttributes = $printerAttributes;
        $this->unsupportedAttributes = $unsupportedAttributes;
    }

    public function encode()
    {
        // Version Number
        $binary = $this->versionNumber->encode();
        // Operation ID
        $binary .= $this->operation->encode();
        // Request ID
        $binary .= $this->requestId->encode();
        // Operation Attribute Group
        $binary .= $this->operationAttributes->encode();
        // Job Attribute Group
        if(!empty($this->jobAttributes)){
            $binary .= $this->jobAttributes->encode();
        }
        // Printer Attribute Group
        if(!empty($this->printerAttributes)){
            $binary .= $this->printerAttributes->encode();
        }
        // Unsupported Attribute Group
        if(!empty($this->UnsupportedAttributes)){
            $binary .= $this->UnsupportedAttributes->encode();
        }
        // End of Attributes Tag
        $binary .= pack('c',0x03); // end-of-attributes-tag
        // Document Data
        if(!empty($this->document)){
            $binary .= $this->document->encode();
        }
        return $binary;
    }

    public function decode($binary)
    {
        $unpacked = unpack("cMajor/cMinor/nStatusCode/NRequestID", $binary);
        
        $this->versionNumber = new \obray\ipp\types\VersionNumber($unpacked['Major'] . '.' . $unpacked['Minor']);
        $this->statusCode = new \obray\ipp\types\StatusCode($unpacked['StatusCode']);
        $this->requestId = new \obray\ipp\types\Integer($unpacked['RequestID']);
        
        $offset = 8;
        
        // decode operation attributes
        $this->operationAttributes = new \obray\ipp\OperationAttributes();
        $newTag = $this->operationAttributes->decode($binary, $offset);
        
        // decode job attributes
        if($newTag!==false && $newTag === 0x02){
            $this->jobAttributes = [];
            while($newTag !== false && $newTag === 0x02){
                $jobAttributes = new \obray\ipp\JobAttributes();
                $newTag = $jobAttributes->decode($binary, $offset);
                $this->jobAttributes[] = $jobAttributes;
            }
        }
        
        // decode printer attributes
        if($newTag!==false && $newTag === 0x04){
            $this->printerAttributes = [];
            while($newTag !== false && $newTag === 0x04){
                $printerAttributes = new \obray\ipp\PrinterAttributes();
                $newTag = $printerAttributes->decode($binary, $offset);
                $this->printerAttributes[] = $printerAttributes;
            }
        }  
    }

}