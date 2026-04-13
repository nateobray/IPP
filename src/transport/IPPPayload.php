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
    public $subscriptionAttributes;
    public $unsupportedAttributes;
    private $document;

    public function __construct(
        ?\obray\ipp\types\VersionNumber $versionNumber = null,
        ?\obray\ipp\types\Operation $operation = null,
        ?\obray\ipp\types\Integer $requestId = null,
        ?\obray\ipp\types\OctetString $document = null,
        ?\obray\ipp\OperationAttributes $operationAttributes = null,
        ?\obray\ipp\JobAttributes $jobAttributes = null,
        ?\obray\ipp\PrinterAttributes $printerAttributes = null,
        ?\obray\ipp\UnsupportedAttributes $unsupportedAttributes = null,
        ?\obray\ipp\SubscriptionAttributes $subscriptionAttributes = null)
    {
        $this->versionNumber = $versionNumber;
        $this->operation = $operation;
        $this->requestId = $requestId;
        $this->document = $document;
        $this->operationAttributes = $operationAttributes;
        $this->jobAttributes = $jobAttributes;
        $this->printerAttributes = $printerAttributes;
        $this->subscriptionAttributes = $subscriptionAttributes;
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
        // Subscription Attribute Group
        if(!empty($this->subscriptionAttributes)){
            $binary .= $this->subscriptionAttributes->encode();
        }
        // Unsupported Attribute Group
        if(!empty($this->unsupportedAttributes)){
            $binary .= $this->unsupportedAttributes->encode();
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
        $unpacked = \obray\ipp\transport\DecodeGuard::unpack(
            "cMajor/cMinor/nStatusCode/NRequestID",
            $binary,
            0,
            8,
            'IPP header'
        );
        
        $this->versionNumber = new \obray\ipp\types\VersionNumber($unpacked['Major'] . '.' . $unpacked['Minor']);
        $this->statusCode = new \obray\ipp\types\StatusCode($unpacked['StatusCode']);
        $this->requestId = new \obray\ipp\types\Integer($unpacked['RequestID']);
        
        $offset = 8;
        if (\obray\ipp\transport\DecodeGuard::readByte($binary, $offset, 'operation-attributes-tag') !== 0x01) {
            throw new \UnexpectedValueException('Expected operation-attributes-tag at offset 8.');
        }
        
        $this->operationAttributes = new \obray\ipp\OperationAttributes();
        $newTag = $this->operationAttributes->decode($binary, $offset);
        
        if($newTag!==false && $newTag === 0x02){
            $this->jobAttributes = [];
            while($newTag !== false && $newTag === 0x02){
                $jobAttributes = new \obray\ipp\JobAttributes();
                $newTag = $jobAttributes->decode($binary, $offset);
                $this->jobAttributes[] = $jobAttributes;
            }
        }
        
        if($newTag!==false && $newTag === 0x04){
            $this->printerAttributes = [];
            while($newTag !== false && $newTag === 0x04){
                $printerAttributes = new \obray\ipp\PrinterAttributes();
                $newTag = $printerAttributes->decode($binary, $offset);
                $this->printerAttributes[] = $printerAttributes;
            }
        }

        if($newTag!==false && $newTag === 0x06){
            $this->subscriptionAttributes = [];
            while($newTag !== false && $newTag === 0x06){
                $subscriptionAttributes = new \obray\ipp\SubscriptionAttributes();
                $newTag = $subscriptionAttributes->decode($binary, $offset);
                $this->subscriptionAttributes[] = $subscriptionAttributes;
            }
        }

        if($newTag!==false && $newTag === 0x05){
            $this->unsupportedAttributes = [];
            while($newTag !== false && $newTag === 0x05){
                $unsupportedAttributes = new \obray\ipp\UnsupportedAttributes();
                $newTag = $unsupportedAttributes->decode($binary, $offset);
                $this->unsupportedAttributes[] = $unsupportedAttributes;
            }
        }

        if ($newTag !== false) {
            throw new \UnexpectedValueException(sprintf(
                'Unexpected attribute group tag 0x%02x at offset %d.',
                $newTag,
                $offset
            ));
        }

        if ($offset === strlen($binary)) {
            throw new \UnexpectedValueException('Missing end-of-attributes tag.');
        }

        if (\obray\ipp\transport\DecodeGuard::readByte($binary, $offset, 'end-of-attributes tag') !== 0x03) {
            throw new \UnexpectedValueException(sprintf(
                'Expected end-of-attributes tag at offset %d.',
                $offset
            ));
        }

        $offset += 1;
        if ($offset < strlen($binary)) {
            $this->document = new \obray\ipp\types\OctetString(substr($binary, $offset));
        }
    }

}
