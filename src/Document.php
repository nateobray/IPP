<?php

namespace obray\ipp;

/**
 * Document Object (PWG5100.5)
 *
 * Wraps a (printerURI, jobId, documentNumber) tuple and exposes the
 * PWG5100.5 Document Object operations:
 *   - getDocumentAttributes (0x0033)
 *   - setDocumentAttributes (0x0035)
 *   - cancelDocument        (0x0036)
 */
class Document
{
    private readonly string $printerURI;
    private readonly int|string $jobID;
    private readonly int $documentNumber;
    private readonly ?string $user;
    private readonly ?string $password;
    private readonly array $curlOptions;
    private string $requestClass;

    public function __construct(
        string $printerURI,
        int|string $jobID,
        int $documentNumber,
        ?string $user = null,
        ?string $password = null,
        array $curlOptions = [],
        ?string $requestClass = null
    ) {
        $this->printerURI     = $printerURI;
        $this->jobID          = $jobID;
        $this->documentNumber = $documentNumber;
        $this->user           = $user;
        $this->password       = $password;
        $this->curlOptions    = $curlOptions;
        $this->requestClass   = $requestClass ?? \obray\ipp\Request::class;
    }

    public function setRequestClass(string $requestClass): self
    {
        $this->requestClass = $requestClass;
        return $this;
    }

    private function createOperationAttributes(): \obray\ipp\OperationAttributes
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        if (is_string($this->jobID)) {
            $operationAttributes->{'job-uri'} = $this->jobID;
        } else {
            $operationAttributes->{'printer-uri'}  = $this->printerURI;
            $operationAttributes->{'job-id'}        = $this->jobID;
        }
        $operationAttributes->{'document-number'} = $this->documentNumber;
        if (!empty($this->user)) {
            $operationAttributes->{'requesting-user-name'} = $this->user;
        }
        return $operationAttributes;
    }

    private function sendPayload(\obray\ipp\transport\IPPPayload $payload): \obray\ipp\transport\IPPPayload
    {
        $requestClass = $this->requestClass;
        $targetURI    = is_string($this->jobID) ? $this->jobID : $this->printerURI;
        return $requestClass::send(
            $targetURI,
            $payload->encode(),
            $this->user,
            $this->password,
            $this->curlOptions
        );
    }

    private function buildPayload(
        int $operationCode,
        int $requestId,
        \obray\ipp\OperationAttributes $operationAttributes,
        ?\obray\ipp\DocumentAttributes $documentAttributes = null,
        string $versionNumber = '2.0'
    ): \obray\ipp\transport\IPPPayload {
        \obray\ipp\spec\OperationRequestValidator::validate(
            $operationCode,
            $operationAttributes
        );

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber($versionNumber),
            new \obray\ipp\types\Operation($operationCode),
            new \obray\ipp\types\Integer($requestId),
            null,
            $operationAttributes
        );

        if ($documentAttributes !== null) {
            $payload->documentAttributes = $documentAttributes;
        }

        return $payload;
    }

    /**
     * Get-Document-Attributes (PWG5100.5 §3.3.1)
     *
     * @param int        $requestId           Client request id
     * @param array|null $requestedAttributes Subset of attributes to retrieve
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function getDocumentAttributes(int $requestId = 1, ?array $requestedAttributes = null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        if ($requestedAttributes !== null) {
            $operationAttributes->{'requested-attributes'} = $requestedAttributes;
        }

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_DOCUMENT_ATTRIBUTES,
                $requestId,
                $operationAttributes
            )
        );
    }

    /**
     * Set-Document-Attributes (PWG5100.5 §3.3.2)
     *
     * @param array $attributes Associative array of document attributes to set
     * @param int   $requestId  Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function setDocumentAttributes(array $attributes, int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        $documentAttributes  = new \obray\ipp\DocumentAttributes();
        foreach ($attributes as $name => $value) {
            $documentAttributes->set($name, $value);
        }

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::SET_DOCUMENT_ATTRIBUTES,
                $requestId,
                $operationAttributes,
                $documentAttributes
            )
        );
    }

    /**
     * Cancel-Document (PWG5100.5 §3.3.3)
     *
     * @param int $requestId Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function cancelDocument(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CANCEL_DOCUMENT,
                $requestId,
                $operationAttributes
            )
        );
    }
}
