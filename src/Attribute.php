<?php
namespace obray\ipp;

class Attribute implements \JsonSerializable
{
    protected $name;
    private $valueTag;
    private $nameLength;
    private $valueLength;
    private $value;
    private $offset;
    private $previousNameKey;

   
    public function __construct($name=NULL, $value=NULL, int $type=NULL, int $maxLength=NULL, string $naturalLanguage=NULL)
    {
        if($name===NULL){
            return $this;
        }
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($name);

        if($value===NULL){
            return $this;
        }
        $this->getValue($type, $value, $naturalLanguage, $maxLength);
        

        $this->valueTag = $this->value->getValueTag();
        $this->valueLength = new \obray\ipp\types\basic\SignedShort($this->value->getLength());
    }

    public function encode()
    {
        $binary = pack('c',$this->valueTag);
        $binary .= $this->nameLength->encode();
        $binary .= $this->name->encode();
        $binary .= $this->valueLength->encode();
        $binary .= $this->value->encode();
        return $binary;
    }

    public function decode($binary, $offset=0, $debugExit=0)
    {
        if(!empty($this->nameLength) && $this->nameLength->getValue()!==0){
            $this->previousNameKey = $this->name->getValue();
        }
        // unpack the attribute value tag
        $this->valueTag = (unpack('cValueTag', $binary, $offset))['ValueTag'];
        $offset += 1;
                
        // decode the name length and adjust offset
        $this->nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->nameLength->len();
        
        // decode the attribute name and adjust offset
        $this->name = (new \obray\ipp\types\basic\LocalizedString(NULL))->decode($binary, $offset, $this->nameLength->getValue());
        $offset += $this->name->len();        
        
        // decode the value length and adjust offset
        $this->valueLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->valueLength->len();
        
        // get the correct value type and decode
        $this->getValue($this->valueTag);
		
        $this->value->decode($binary, $offset, $this->valueLength->getValue());
        $offset += $this->valueLength->getValue();
        
        // set offset for retreival of next attribute
        $this->offset = $offset;

        return $this;
    }

    private function getValue($type, $value=NULL, $natuarlLanguage=NULL, $maxLength=NULL)
    {
        switch($type){
            case \obray\ipp\enums\Types::BOOLEAN:
                $this->value = new \obray\ipp\types\Boolean($value);
                break;
            case \obray\ipp\enums\Types::CHARSET:
                $this->value = new \obray\ipp\types\Charset($value);
                break;
            case \obray\ipp\enums\Types::DATETIME:
                $this->value = new \obray\ipp\types\DateTime($value);
                break;
            case \obray\ipp\enums\Types::ENUM:
                $nameToSwitchOn = $this->name->getValue();
                if(empty($nameToSwitchOn)){
                    $nameToSwitchOn = $this->previousNameKey;
                }
                switch($nameToSwitchOn){
                    case 'orientation-requested': case 'orientation-requested-supported':
                        $this->value = new \obray\ipp\enums\OrientationRequested();
                        break;
                    case 'job-state':
                        $this->value = new \obray\ipp\enums\JobState($value);
                        break;
                    default:
                        $this->value = new \obray\ipp\types\Integer();
                        break;
                }
                break;
            case \obray\ipp\enums\Types::INTEGER:
                $this->value = new \obray\ipp\types\Integer($value);
                break;
            case \obray\ipp\enums\Types::KEYWORD:
                $this->value = new \obray\ipp\types\Keyword($value);
                break;
            case \obray\ipp\enums\Types::MIMEMEDIATYPE:
                $this->value = new \obray\ipp\types\MimeMediaType($value);
                break;
            case \obray\ipp\enums\Types::NAME:
                if($maxLength!=NULL && strlen($value)>$maxLength){
                    $value = substr($value,0,$maxLength);
                }
                if(!empty($naturalLanguage)){
                    $this->value = new \obray\ipp\types\NameWithLangauge($naturalLanguage, $value);
                } else {
                    $this->value =new \obray\ipp\types\NameWithoutLanguage($value);
                }
                break;
            case \obray\ipp\enums\Types::NATURALLANGUAGE:
                $this->value = new \obray\ipp\types\NaturalLanguage($value);
                break;
            case \obray\ipp\enums\Types::OCTETSTRING:
                $this->value = new \obray\ipp\types\OctetString($value);
                break;
            case \obray\ipp\enums\Types::RANGEOFINTEGER:
                $value = explode('-',$value);
                if(count($value)!=2){
                    $value = array(0=>0, 1=>0);
                }
                $this->value = new \obray\ipp\types\RangeOfInteger($value[0], $value[1]);
                break;
            case \obray\ipp\enums\Types::RESOLUTION:
                $value = explode('x',$value);
                if(count($value)!=3){
                    $value = array(0=>0, 1=>0, 2=>0);
                }
                $this->value = new \obray\ipp\types\Resolution($value[0], $value[1], $value[2]);
                break;
            case \obray\ipp\enums\Types::STATUSCODE:
                $this->value = new \obray\ipp\types\StatusCode($value);
                break;
            case \obray\ipp\enums\Types::TEXT:
                if($maxLength!=NULL && strlen($value)>$maxLength){
                    $value = substr($value,0,$maxLength);
                }
                if(!empty($naturalLanguage)){
                    $this->value = new \obray\ipp\types\TextWithLangauge($naturalLanguage, $value);
                } else {
                    $this->value = new \obray\ipp\types\TextWithoutLangauge($value);
                }
                break;
            case \obray\ipp\enums\Types::TEXTWITHOUTLANGUAGE:
                $this->value = new \obray\ipp\types\TextWithoutLanguage($value);
                break;
            case \obray\ipp\enums\Types::NAMEWITHOUTLANGUAGE:
                $this->value = new \obray\ipp\types\NameWithoutLanguage($value);
                break;
            case \obray\ipp\enums\Types::URI:
                $this->value = new \obray\ipp\types\URI($value);
                break;
            case \obray\ipp\enums\Types::URISCHEME:
                $this->value = new \obray\ipp\types\URIScheme($value);
                break;
            //case \obray\ipp\enums\Types::VERSIONNUMBER:
            //    $this->value = new \obray\ipp\types\VersionNumber($value);
            //    break;
            case \obray\ipp\enums\Types::NOVAL:
                $this->value = new \obray\ipp\types\NoVal($value);
                break;
            case \obray\ipp\enums\Types::UNKNOWN:
                $this->value = new \obray\ipp\types\Unknown($value);
            break;
            default:
                throw new \Exception("The type specified does not exists.");
                break;
        }
        
    }

    public function getName()
    {
        return $this->name->__toString();
    }

    public function getNameLength()
    {
        return $this->nameLength->getValue();
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function jsonSerialize()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value->__toString();
    }

}