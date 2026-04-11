<?php
declare(strict_types=1);

namespace obray\ipp\exceptions;

class IppStatusException extends \RuntimeException
{
    private string $printerURI;
    private \obray\ipp\transport\IPPPayload $response;

    public function __construct(string $printerURI, \obray\ipp\transport\IPPPayload $response)
    {
        $this->printerURI = $printerURI;
        $this->response = $response;
        $statusCode = (string) $response->statusCode;
        parent::__construct(
            'IPP error from ' . $printerURI . ': ' . $statusCode,
            $response->statusCode->getValue()
        );
    }

    public function getPrinterURI(): string
    {
        return $this->printerURI;
    }

    public function getResponse(): \obray\ipp\transport\IPPPayload
    {
        return $this->response;
    }

    public function getStatusCode(): \obray\ipp\types\StatusCode
    {
        return $this->response->statusCode;
    }
}
