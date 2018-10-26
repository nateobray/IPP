<?php 

namespace obray\ipp;

/*
 * Operation Attributes
 * 
 * @property string $charset            This operation attribute identifies the charset (coded
                                        character set and encoding method) used by any ’text’ and
                                        ’name’ attributes that the client is supplying in this request
 * @property string $naturalLanguage    This operation attribute identifies the natural language used 
 *                                      by any ’text’ and ’name’ attributes that the client is supplying 
 *                                      in this request.
 * @property string $statusCode         
 * @property string $statusMessage
 * @property string $detailedStatusMessage
 * @property string $documentURI
 * @property string $target
 * @property string $userName
 * @property string $jobName
 * @property string $ippAttributeFidelity
 * @property string $documentName
 * @property string $compression
 * @peoperty string $documentType
 * @property string $naturalLanguage
 * @property string $jobKOctets
 * @property string $jobImpressions
 * @property string $jobMediaSheets
*/

class OperationAttributes
{
    private $attribute_group_tag = 0x01;

    public $charset;
    public $naturalLanguage;
    public $statusCode;
    public $statusMessage;
    public $detailedStatusMessage;
    public $documentAccessError;
    public $documentURI;
    public $target;
    public $userName;
    public $jobName;
    public $ippAttributeFidelity;
    public $documentName;
    public $compression;
    public $documentType;
    public $jobKOctets;
    public $jobImpressions;
    public $jobMediaSheets;

    public function __construct(){
        $this->charset = new \obray\ipp\types\Charset('utf-8');
        $this->naturalLanguage = new \obray\ipp\types\NaturalLanguage('en');
    }

    public function __set(string $name, $value, string $naturalLanguage=NULL)
    {
        switch($name){
            case 'charset':
                $this->$name = new \obray\ipp\types\Attribute($name, new \obray\ipp\types\Charset($value));
                break;
            case 'naturalLanguage':
                $this->$name = new \obray\ipp\types\Attribute($name, new \obray\ipp\types\NaturalLanguage($value));
                break;
            case 'statusCode':
                $this->$name = new \obray\ipp\attributes\StatusCode($value);
                break;
            case 'statusMessage':
                $this->$name = new \obray\ipp\attributes\Text('status-message', $value, $naturalLanguage,255);
                break;
            case 'detailedStatusMessage':
                $this->name = new \obray\ipp\attributes\Text('detailed-status-message', $value, $naturalLanguage,\obray\ipp\attributes\Text::MaxLength);
                break;
            case 'documentAccessError':
                $this->name = new \obray\ipp\attributes\Text('document-access-error', $value, $naturalLanguage,\obray\ipp\attributes\Text::MaxLength);
                break;
            case 'printerURI':
                break;
            case 'jobURI':
                break;
            case 'jobID':
                break;
            case 'documentURI':
                
                break;
            case 'target':
                break;
            case 'userName':
                break;
            case 'jobName':
                break;
            case 'ippAttributeFidelity':
                break;
            case 'documentName':
                break;
            case 'compression':
                break;
            case 'documentType':
                break;
            case 'jobKOctets':
                break;
            case 'jobImpressions':
                break;
            case 'jobMediaSheets':
                break;

        }
    }

    public function validate(array $attributeKeys)
    {
        if(empty($charset) || $charset !== 'utf-8'){
            throw new obray\exceptions\ClientErrorCharsetNotSupported();
        }
        if(empty($natuarlLanguage) && $naturalLanguage !== 'en'){
            throw new \Exception("Invalid request.");
        }
    }

    public function encode()
    {
        $binary = '';
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        //print_r($properties);
        
        forEach($properties as $property){
            
            if( !empty($this->{$property->name}) && method_exists($this->{$property->name}, 'encode') ){
                print_r("\tEncoding ".$property->name."\n");
                $binary .= $this->{$property->name}->encode();
            }
        }
        

        return $binary;
    }
}