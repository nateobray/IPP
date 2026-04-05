<?php
declare(strict_types=1);

namespace obray\ipp\exceptions;

class NetworkError extends \RuntimeException
{
    private string $printerURI;
    private int $curlErrorCode;

    public function __construct(string $printerURI, int $curlErrorCode, string $curlErrorMessage = '')
    {
        $this->printerURI = $printerURI;
        $this->curlErrorCode = $curlErrorCode;
        $message = 'Network error while contacting printer: ' . $printerURI;
        if ($curlErrorMessage !== '') {
            $message .= ' (' . $curlErrorMessage . ')';
        }
        parent::__construct($message, $curlErrorCode);
    }

    public function getPrinterURI(): string
    {
        return $this->printerURI;
    }

    public function getCurlErrorCode(): int
    {
        return $this->curlErrorCode;
    }
}
