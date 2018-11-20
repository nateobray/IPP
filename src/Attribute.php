<?php
namespace obray\ipp;

class Attribute
{
    protected $name;
    private $valueTag;
    private $nameLength;
    private $valueLength;
    private $value;
    private $offset;

   
    public function __construct($name=NULL, $value=NULL, int $type=NULL, int $maxLength=NULL, string $naturalLanguage=NULL)
    {
        if($name===NULL){
            return $this;
        }
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($name);

        $this->getValue($type);

        

        $this->valueTag = $this->value->getValueTag();
        $this->valueLength = new \obray\ipp\types\basic\SignedShort($this->value->getLength());
    }

    public function encode()
    {
        print_r("Encoding: (".$this->valueTag.") ".$this->name."\n");
        $binary = pack('c',$this->valueTag);
        $binary .= $this->nameLength->encode();
        $binary .= $this->name->encode();
        $binary .= $this->valueLength->encode();
        $binary .= $this->value->encode();

        print_r(unpack('cValueTag/sNameLength/a'.$this->nameLength.'Name/sValueLength/a'.$this->valueLength.'Value',$binary));
        
        return $binary;
    }

    public function decode($binary, $offset=0)
    {
        // unpack the attribute value tag
        $this->valueTag = (unpack('cValueTag', $binary, $offset))['ValueTag'];
        
        // decode the name length and adjust offset
        $this->nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, ++$offset);
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

    private function getValue($type, $value=NULL)
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
                $this->value = new \obray\ipp\types\Enum($value);
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
                    throw new \Exception("Invalid range provided: ".$value);
                }
                $this->value = new \obray\ipp\types\RangeOfInteger($value[0], $value[1]);
                break;
            case \obray\ipp\enums\Types::RESOLUTION:
                $value = explode('x',$value);
                if(count($value)!=3){
                    throw new \Exception("Invalid resolution provided: ".$value);
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
            case \obray\ipp\enums\Types::URI:
                $this->value = new \obray\ipp\types\URI($value);
                break;
            case \obray\ipp\enums\Types::URISCHEME:
                $this->value = new \obray\ipp\types\URIScheme($value);
                break;
            case \obray\ipp\enums\Types::VERSIONNUMBER:
                $this->value = new \obray\ipp\types\VersionNumber($value);
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

    public function getOffset()
    {
        return $this->offset;
    }

}