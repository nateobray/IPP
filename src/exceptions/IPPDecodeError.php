<?php
declare(strict_types=1);

namespace obray\ipp\exceptions;

class IPPDecodeError extends \RuntimeException
{
    private string $printerURI;

    public function __construct(string $printerURI, \Throwable $previous)
    {
        $this->printerURI = $printerURI;
        parent::__construct('Failed to decode IPP response from ' . $printerURI, 0, $previous);
    }

    public function getPrinterURI(): string
    {
        return $this->printerURI;
    }
}
