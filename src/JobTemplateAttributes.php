<?php
namespace obray\ipp;

class JobTemplateAttributes
{
    public $jobPriority;
    public $jobHoldUntil;
    public $jobSheets;
    public $multipleDocumentHandling;
    public $copies;
    public $finishings;
    public $pageRanges;
    public $sides;
    public $numberUp;
    public $orientationRequested;
    public $media;
    public $printerResolution;
    public $printQuality;

    public function __set(string $name, $value)
    {
        switch($name){
            case 'jobPriority': 
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Unsupported job priority value.");
                }
                $this->$name = $value;
                break;
            case 'jobHoldUntil': 
                $this->jobHoldUntil = new \obray\enums\JobHoldUntil($value);
                break;
            case 'jobSheets':
                $this->jobSheets = new \obray\enums\JobSheets($value);
                break;
            case 'multipleDocumentHandling':
                $this->multipleDocumentHandling = new \obray\enumbs\MultipleDocumentHandling($value);
                break;
            case 'copies':
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Unsupported job priority value.");
                }
                $this->$name = $value;
                break;
            case 'finishings':
                $this->$name = new \obray\enums\Finishings($value);
                break;
            case 'pageRanges':
                $this->$name = new \obray\enums\PageRanges($value);
                break;
            case 'sides':
                $this->sides = new \obray\enums\Sides($value);
                break;
            case 'numberUp':
                if(!is_int($value) || $value < 0){
                    throw new \Exception("Unsupported number up value.");
                }
                $this->$name = $value;
                break;
            case 'orientationRequested':
                $this->$name = new \obray\enums\OrientationRequested($value);
                break;
            case 'media':
                $this->$name = $value;
                break;
            case 'printerResolution':
                $this->$name = new \obray\enums\Resolution($value);
                break;
            case 'printQuality':
                $this->$name = new \obray\enums\PrintQuality($value);
                break;
            default:
                throw new \Exception("Invalid parameter ".$name." specified.");
                break;
                
        }
    }

}