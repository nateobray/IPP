<?php

namespace obray;

class JobDescriptionAttributes
{
    private $attribute_group_tag = 0x02;
    
    public $jobURI;
    public $jobId;
    public $jobPrinterUri;
    public $jobMoreInfo;
    public $jobName;
    public $jobOriginatingUserName;
    public $jobState;
    public $jobStateReasons;
    public $jobStateMessage;
    public $jobDetailedStatusMessages;
    public $jobDocumentAccessErrors;
    public $numberOfDocuments;
    public $outputDeviceAssigned;
    public $timeAtCreation;
    public $timeAtProcessing;
    public $timeAtCompleted;
    public $jobPrinterUpTime;
    public $dateTimeAtCreation;
    public $dateTimeAtProcessing;
    public $dateTimeAtCompleted;
    public $numberOfInterveningJobs;
    public $jobMessageFromOperator;
    public $jobKOctets;
    public $jobImpressions;
    public $jobMediaSheets;
    public $jobKOctetsProcessed;
    public $jobImpressionsCompleted;
    public $jobMediaSheetsCompleted;
    public $attributesCharset;
    public $attributesNaturalLanguage;

    public function __set(string $name, $value)
    {
        switch($name) {
            case 'jobURI';
                $this->$name = new \obray\types\URI($value);
                break;
            case 'jobId';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Job Id.");
                }
                $this->$name = $value;
                break;
            case 'jobPrinterUri';
                $this->$name = new \obray\types\URI($value);
                break;
            case 'jobMoreInfo';
                $this->$name = new \obray\types\URI($value);
                break;
            case 'jobName';
                $this->$name = (string)$value;
                break;
            case 'jobOriginatingUserName';
                $this->$name = (string)$value;
                break;
            case 'jobState';
                $this->$name = new \obray\enums\JobState($value);
                break;
            case 'jobStateReasons';
                $this->$name = new \obray\enums\JobStateReasons($value);
                break;
            case 'jobStateMessage';
                $this->$name = (string)$value;
                break;
            case 'jobDetailedStatusMessages';
                $this->$name = (string)$value;
                break;
            case 'jobDocumentAccessErrors';
                $this->$name = (string)$value;
                break;
            case 'numberOfDocuments';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Documents.");
                }
                $this->$name = $value;
                break;
            case 'outputDeviceAssigned';
                $this->$name = (string)$value;
                break;
            case 'timeAtCreation';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Time at Creation.");
                }
                $this->$name = $value;
                break;
            case 'timeAtProcessing';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Time at Processing.");
                }
                $this->$name = $value;
                break;
            case 'timeAtCompleted';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Time at Completed.");
                }
                $this->$name = $value;
                break;
            case 'jobPrinterUpTime';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Printer Up Time.");
                }
                $this->$name = $value;
                break;
            case 'dateTimeAtCreation';
                $this->$name = (string)$value;
                break;
            case 'dateTimeAtProcessing';
                $this->$name = (string)$value;
                break;
            case 'dateTimeAtCompleted';
                $this->$name = (string)$value;
                break;
            case 'numberOfInterveningJobs';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Intervening Jobs.");
                }
                $this->$name = $value;
                break;
            case 'jobMessageFromOperator';
                $this->$name = (string)$value;
                break;
            case 'jobKOctets';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Intervening Jobs.");
                }
                $this->$name = $value;
                break;
            case 'jobImpressions';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Intervening Jobs.");
                }
                $this->$name = $value;
                break;
            case 'jobMediaSheets';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Intervening Jobs.");
                }
                $this->$name = $value;
                break;
            case 'jobKOctetsProcessed';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Intervening Jobs.");
                }
                $this->$name = $value;
                break;
            case 'jobImpressionsCompleted';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Intervening Jobs.");
                }
                $this->$name = $value;
                break;
            case 'jobMediaSheetsCompleted';
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Invalid Number of Intervening Jobs.");
                }
                $this->$name = $value;
                break;
            case 'attributesCharset';
                $this->$name = (string)$value;
                break;
            case 'attributesNaturalLanguage';
                $this->$name = (string)$value;
                break;
            default:
                throw new \Exception("Invalide attribute ".$name.".");
                break;
        }
    }
}