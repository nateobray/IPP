<?php
namespace obray\ipp\enums;

class Types extends \obray\ipp\types\Enum
{
    const UNSUPPORTED = 0x10;
    const UNKNOWN = 0x12;
    const NOVALUE = 0x13;
    const BOOLEAN = 0x22;
    const CHARSET = 0x47;
    const DATETIME = 0x31;
    const ENUM = 0x23;
    const INTEGER = 0x21;
    const KEYWORD = 0x44;
    const MIMEMEDIATYPE = 0x49;
    const NAME = 0x0008;
    const NATURALLANGUAGE = 0x48;
    const OCTETSTRING = 0x30;
    const RANGEOFINTEGER = 0x33;
    const COLLECTION = 0x34;
    const TEXTWITHLANGUAGE = 0x35;
    const NAMEWITHLANGUAGE = 0x36;
    const TEXTWITHOUTLANGUAGE = 0x41;
    const NAMEWITHOUTLANGUAGE = 0x42;
    const RESOLUTION = 0x32;
    const STATUSCODE = 0x000D;
    const TEXT = 0x000E;
    const URI = 0x45;
    const URISCHEME = 0x46;
    const NOVAL = 0x13;

    public static function getType($type, $value=NULL, $naturalLanguage=NULL, $maxLength=NULL, $nameToSwitchOn=NULL)
    {
        switch($type){
            case \obray\ipp\enums\Types::BOOLEAN:
                return new \obray\ipp\types\Boolean($value);
                break;
            case \obray\ipp\enums\Types::CHARSET:
                return new \obray\ipp\types\Charset($value);
                break;
            case \obray\ipp\enums\Types::DATETIME:
                return new \obray\ipp\types\DateTime($value);
                break;
            case \obray\ipp\enums\Types::ENUM:
                
                switch($nameToSwitchOn){
                    case 'orientation-requested': case 'orientation-requested-supported':
                        return new \obray\ipp\enums\OrientationRequested();
                        break;
                    case 'job-state':
                        return new \obray\ipp\enums\JobState($value);
                        break;
                    case 'finishings':
                        return new \obray\ipp\enums\Finishings($value);
                        break;
                    case 'orientation-requested':
                        return new \obray\ipp\enums\OrientationRequested($value);
                        break;
                    case 'print-quality':
                        return new \obray\ipp\enums\PrintQuality($value);
                        break;
                    default:
                        return new \obray\ipp\types\Integer($value);
                        break;
                }
                break;
            case \obray\ipp\enums\Types::INTEGER:
                return new \obray\ipp\types\Integer($value);
                break;
            case \obray\ipp\enums\Types::KEYWORD:
                return new \obray\ipp\types\Keyword($value);
                break;
            case \obray\ipp\enums\Types::MIMEMEDIATYPE:
                return new \obray\ipp\types\MimeMediaType($value);
                break;
            case \obray\ipp\enums\Types::NAME:
                if($maxLength!=NULL && strlen($value)>$maxLength){
                    $value = substr($value,0,$maxLength);
                }
                if(!empty($naturalLanguage)){
                    return new \obray\ipp\types\NameWithLanguage($naturalLanguage, $value);
                } else {
                    return new \obray\ipp\types\NameWithoutLanguage($value);
                }
                break;
            case \obray\ipp\enums\Types::NAMEWITHLANGUAGE:
                if(empty($naturalLanguage)){
                    $natuarlLanguage = "en-us";
                    //throw new \Exception("Natural language must be specified.");
                }
                return new \obray\ipp\types\NameWithLanguage($naturalLanguage, $value);
                break;
            case \obray\ipp\enums\Types::NATURALLANGUAGE:
                return new \obray\ipp\types\NaturalLanguage($value);
                break;
            case \obray\ipp\enums\Types::OCTETSTRING:
                return new \obray\ipp\types\OctetString($value);
                break;
            case \obray\ipp\enums\Types::RANGEOFINTEGER:
                $value = explode('-',$value);
                if(count($value)!=2){
                    $value = array(0=>0, 1=>0);
                }
                return new \obray\ipp\types\RangeOfInteger($value[0], $value[1]);
                break;
            case \obray\ipp\enums\Types::RESOLUTION:
                $value = explode('x', $value);
                if(count($value)!=2){
                    $value = array(0=>0, 1=>0);
                }
                if(strpos($value[1], 'dpi')!==false){
                    $value[1] = trim(str_replace('dpi', '', $value[1]));
                    $value[2] = 3;
                }
                if(strpos($value[1], 'dpc')!==false){
                    
                    $value[1] = trim(str_replace('dpc', '', $value[1]));
                    $value[2] = 4;
                }
                if(count($value)<3){
                    $value[2] = 3;
                }
                return new \obray\ipp\types\Resolution($value[0], $value[1], $value[2]);
                break;
            case \obray\ipp\enums\Types::STATUSCODE:
                return new \obray\ipp\types\StatusCode($value);
                break;
            case \obray\ipp\enums\Types::TEXT:
                if($maxLength!=NULL && strlen($value)>$maxLength){
                    $value = substr($value,0,$maxLength);
                }
                if(!empty($naturalLanguage)){
                    return new \obray\ipp\types\TextWithLanguage($naturalLanguage, $value);
                } else {
                    return new \obray\ipp\types\TextWithoutLanguage($value);
                }
                break;
            case \obray\ipp\enums\Types::TEXTWITHLANGUAGE:
                return new \obray\ipp\types\TextWithLanguage($naturalLanguage, $value);
                break;
            case \obray\ipp\enums\Types::TEXTWITHOUTLANGUAGE:
                return new \obray\ipp\types\TextWithoutLanguage($value);
                break;
            case \obray\ipp\enums\Types::NAMEWITHOUTLANGUAGE:
                return new \obray\ipp\types\NameWithoutLanguage($value);
                break;
            case \obray\ipp\enums\Types::URI:
                return new \obray\ipp\types\URI($value);
                break;
            case \obray\ipp\enums\Types::URISCHEME:
                return new \obray\ipp\types\URIScheme($value);
                break;
            case \obray\ipp\enums\Types::NOVAL:
                return new \obray\ipp\types\NoVal($value);
                break;
            case \obray\ipp\enums\Types::UNKNOWN:
                return new \obray\ipp\types\Unknown($value);
                break;
            case \obray\ipp\enums\Types::COLLECTION:
                return new \obray\ipp\types\Unknown($value);
                //return new \obray\ipp\types\Collection($value);
                break;
            default:
                return new \obray\ipp\types\Unknown($value);
                //throw new \Exception("The type specified does not exists.");
                break;
        }
    }
}
