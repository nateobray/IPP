<?php
namespace obray;

class PrinterAttributes
{
    public $printerURISupported;
    public $uriSecuritySupported;
    public $uriAuthenticationSupported;
    public $printerName;
    public $printerLocation;
    public $printerInfo;
    public $printerMoreInfo;
    public $printerDriverInstaller;
    public $printerMakeAndModel;
    public $printerMoreInfoManufacturer;
    public $printerState;
    public $printerStateReasons;
    public $printerStateMessage;
    public $ippVersionsSupported;
    public $operationsSupported;
    public $multipleDocumentJobsSupported;
    public $charsetConfigured;
    public $charsetSupported;
    public $naturalLanguageConfigured;
    public $generatedNaturalLanguageSupported;
    public $documentFormatDefault;
    public $documentFormatSupported;
    public $printerIsAcceptingJobs;
    public $queuedJobCount;
    public $printerMessageFromOperator;
    public $colorSupported;
    public $referenceURISchemesSupported;
    public $pdlOverrideSupported;
    public $printerUpTime;
    public $printerCurrentTime;
    public $multipleOperationTimeOut;
    public $compressionSupported;
    public $jobKOctetsSupported;
    public $jobImpressionsSupported;
    public $jobMediaSheetsSupported;
    public $pagesPerMinute;
    public $pagesPerMinuteColor;

    public function __SET(string $name, $value)
    {
        $this->$name = $value;
    }

}