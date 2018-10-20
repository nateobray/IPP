<?php

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
        int $operationId,
        int $requestId,
        \obray\OperationAttributes $operationAttributes,
        \obray\JobTemplateAttributes $jobTemplateAttributes = NULL,
        \obray\JobDescriptionAttributes $jobDescriptionAttributes = NULL,
        \obray\PrinterDescriptionAttributes $printerDescriptionAttributes = NULL,
        \obray\UnsupportedAttributes $unsupportedAttributes = NULL)
    {
        $this->versionNumber = \obray\types\IPPVersionNumber('1.1');
        $this->operationId = $operationId;
        $this->requestId = $requestId;
        $this->operationAttributes = $operationAttributes;
        $this->jobTemplateAttributes = $jobTemplateAttributes;
        $this->jobDescriptionAttributes = $jobDescriptionAttributes;
        $this->printerDescriptionAttributes = $printerDescriptionAttributes;
        $this->unsupportedAttributes = $unsupportedAttributes;
    }

}