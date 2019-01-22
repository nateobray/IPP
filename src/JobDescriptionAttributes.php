<?php

namespace obray\ipp;

class JobDescriptionAttributes implements \JsonSerializable
{
    private $attribute_group_tag = 0x02;
    
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

    public function decode($binary, &$offset=8)
    {	
        $AttributeGroupTag = (unpack("cAttributeGroupTag", $binary, $offset))['AttributeGroupTag'];
        if( $AttributeGroupTag !== $this->attribute_group_tag ){ return false; }
        $validAttributeGroupTags = [0x01,0x02,0x03,0x04,0x05];
        $endOfAttributesTag = 0x03;
        $offset += 1;
        while(true){
            $attribute = (new \obray\ipp\Attribute())->decode($binary, $offset);
            $this->attributes[$attribute->getName()] = $attribute;
            $offset = $attribute->getOffset();
            $newTag = (unpack("cAttributeGroupTag", $binary, $offset))['AttributeGroupTag'];
            if($newTag===$endOfAttributesTag){
                //print_r("end of attributes - break\n");
                return false;
            }
            if(in_array($newTag,$validAttributeGroupTags)){
                //print_r("Found new valid attribute tag.\n");
                return $newTag;
            }       
        }
    }

    public function jsonSerialize()
    {
        return $this->attributes;
    }
}