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
    private $naturalLanguageOverride;

    private $attributes = array();

    public function __construct(){
        $this->attributes['charset'] = new \obray\ipp\Attribute('attributes-charset', 'utf-8', \obray\ipp\enums\Types::CHARSET);
        $this->attributes['naturalLanguage'] = new \obray\ipp\Attribute('attributes-natural-language', 'en', \obray\ipp\enums\Types::NATURALLANGUAGE);
    }

    public function setNaturalLanguage($lang=NULL){
        $this->naturalLanguageOverride = $lang;
    }

    public function __set(string $name, $value)
    {
        switch($name){
            case 'charset':
                $this->attributes[$name] = new \obray\ipp\Attribute('attributes-charset', $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'naturalLanguage':
                $this->attributes[$name] = new \obray\ipp\Attribute('attributes-natural-language', $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'statusCode':
                $this->attributes[$name] = new \obray\ipp\Attribute('status-code', $value, \obray\ipp\enums\Types::STATUSCODE);
                break;
            case 'statusMessage':
                $this->attributes[$name] = new \obray\ipp\Attribute('status-message', $value, \obray\ipp\enums\Types::TEXT, 255, $this->naturalLanguageOverride);
                break;
            case 'detailedStatusMessage':
                $this->attributes[$name] = new \obray\ipp\Attribute('detailed-status-message', $value, \obray\ipp\enums\Types::TEXT, 1024, $this->naturalLanguageOverride);
                break;
            case 'documentAccessError':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-access-error', $value, \obray\ipp\enums\Types::TEXT, $this->naturalLanguageOverride,\obray\ipp\attributes\Text::MaxLength);
                break;
            case 'printerURI':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-uri', $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'jobURI':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-uri', $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'jobID':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-id', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'documentURI':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-uri', $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'requestingUserName':
                $this->attributes[$name] = new \obray\ipp\Attribute('requesting-user-name', $value, \obray\ipp\enums\Types::NAME, 255, $this->naturalLanguageOverride);
                break;
            case 'jobName':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-name', $value, \obray\ipp\enums\Types::NAME, 255, $this->naturalLanguageOverride);
                break;
            case 'ippAttributeFidelity':
                $this->attributes[$name] = new \obray\ipp\Attribute('ipp-attribute-fidelity', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'documentName':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-name', $value, \obray\ipp\enums\Types::NAME, 255, $this->naturalLanguageOverride);
                break;
            case 'compression':
                $this->attributes[$name] = new \obray\ipp\Attribute('compression', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'documentFormat':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-format', $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            case 'documentNaturalLanguage':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-natural-language', $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'jobKOctets':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-k-octets', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'jobImpressions':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-impressions', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'jobMediaSheets':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-media-sheets', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            default:
                throw new \Exception("Invalid operational parameter.");
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
        $binary = pack('c',$this->attribute_group_tag);
        forEach($this->attributes as $name => $attribute){
                //print_r("\tEncoding ".$name."\n");
                $binary .= $attribute->encode();
        }
        return $binary;
    }

    public function decode($binary, $offset)
    {
        
        $AttributeGroupTag = (unpack("cAttributeGroupTag", $binary, 8))['AttributeGroupTag'];
        $validAttributeGroupTags = [0x01,0x02,0x03,0x04];
        $offset = 9; 
        while(true){
            print_r($offset."\n");
            $attribute = (new \obray\ipp\Attribute())->decode($binary, $offset);
            print_r($attribute);
            print_r($attribute->getName()."\n");
            $this->attributes[$attribute->getName()] = $attribute;
            $offset += $attribute->getOffset();
            print_r($offset."\n");
            $newTag = (unpack("cAttributeGroupTag", $binary, $offset))['AttributeGroupTag'];
            //print_r($newTag."\n");
            if(in_array($newTag,$validAttributeGroupTags)){
                print_r("break\n");
                break;
            }
        }
        
    }
}