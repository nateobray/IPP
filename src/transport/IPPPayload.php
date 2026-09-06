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
    private const ATTRIBUTE_GROUPS = [
        0x02 => ['jobAttributes', \obray\ipp\JobAttributes::class],
        0x04 => ['printerAttributes', \obray\ipp\PrinterAttributes::class],
        0x05 => ['unsupportedAttributes', \obray\ipp\UnsupportedAttributes::class],
        0x06 => ['subscriptionAttributes', \obray\ipp\SubscriptionAttributes::class],
        0x07 => ['eventNotificationAttributes', \obray\ipp\EventNotificationAttributes::class],
        0x09 => ['documentAttributes', \obray\ipp\DocumentAttributes::class],
    ];

    public $versionNumber;
    private $operation;
    public $requestId;
    public $statusCode;
    public $operationAttributes;
    public $jobAttributes;
    public $printerAttributes;
    public $subscriptionAttributes;
    public $eventNotificationAttributes;
    public $documentAttributes;
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
        // Document Attribute Group
        if(!empty($this->documentAttributes)){
            $binary .= $this->documentAttributes->encode();
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
        
        foreach (self::ATTRIBUTE_GROUPS as [$property]) {
            $this->{$property} = null;
        }
        $this->document = null;

        // Response group order depends on the operation. In particular,
        // Unsupported Attributes precede Job/Printer Attributes (RFC 8011).
        while ($newTag !== false) {
            if (!isset(self::ATTRIBUTE_GROUPS[$newTag])) {
                throw new \UnexpectedValueException(sprintf(
                    'Unexpected attribute group tag 0x%02x at offset %d.',
                    $newTag,
                    $offset
                ));
            }

            [$property, $class] = self::ATTRIBUTE_GROUPS[$newTag];
            $group = new $class();
            $newTag = $group->decode($binary, $offset);
            $this->{$property}[] = $group;
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
