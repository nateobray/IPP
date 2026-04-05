<?php 

namespace obray\ipp;

use obray\ipp\exceptions\ClientErrorCharsetNotSupported;
use obray\ipp\exceptions\InvalidRequest;

class OperationAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x01;
    private $naturalLanguageOverride;

    private function createKeywordOrNameAttribute(string $name, $value)
    {
        if ($value instanceof \obray\ipp\types\Keyword
            || $value instanceof \obray\ipp\types\NameWithoutLanguage
            || $value instanceof \obray\ipp\types\NameWithLanguage
        ) {
            return new \obray\ipp\Attribute($name, $value);
        }

        return new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::KEYWORD);
    }

    public function __construct(){
        $this->attributes['attributes-charset'] = new \obray\ipp\Attribute('attributes-charset', 'utf-8', \obray\ipp\enums\Types::CHARSET);
        $this->attributes['attributes-natural-language'] = new \obray\ipp\Attribute('attributes-natural-language', 'en', \obray\ipp\enums\Types::NATURALLANGUAGE);
    }

    public function setNaturalLanguage($lang=NULL){
        $this->naturalLanguageOverride = $lang;
    }

    public function set(string $name, $value)
    {
        switch($name){
            case 'attributes-charset':
                $this->attributes[$name] = new \obray\ipp\Attribute('attributes-charset', $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'attributes-natural-language':
                $this->attributes[$name] = new \obray\ipp\Attribute('attributes-natural-language', $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'status-code':
                $this->attributes[$name] = new \obray\ipp\Attribute('status-code', $value, \obray\ipp\enums\Types::STATUSCODE);
                break;
            case 'status-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('status-message', $value, \obray\ipp\enums\Types::TEXT, 255, $this->naturalLanguageOverride);
                break;
            case 'detailed-status-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('detailed-status-message', $value, \obray\ipp\enums\Types::TEXT, 1024, $this->naturalLanguageOverride);
                break;
            case 'document-access-error':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-access-error', $value, \obray\ipp\enums\Types::TEXT);
                break;
            case 'printer-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-uri', $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'job-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-uri', $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'job-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-id', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'document-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-uri', $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'resource-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('resource-name', $value, \obray\ipp\enums\Types::NAME, 255, $this->naturalLanguageOverride);
                break;
            case 'resource-format':
                $this->attributes[$name] = new \obray\ipp\Attribute('resource-format', $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            case 'resource-type':
                $this->attributes[$name] = new \obray\ipp\Attribute('resource-type', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'last-document':
                $this->attributes[$name] = new \obray\ipp\Attribute('last-document', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'requesting-user-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('requesting-user-name', $value, \obray\ipp\enums\Types::NAME, 255, $this->naturalLanguageOverride);
                break;
            case 'job-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-name', $value, \obray\ipp\enums\Types::NAME, 255, $this->naturalLanguageOverride);
                break;
            case 'job-hold-until':
                $this->attributes[$name] = $this->createKeywordOrNameAttribute('job-hold-until', $value);
                break;
            case 'ipp-attribute-fidelity':
                $this->attributes[$name] = new \obray\ipp\Attribute('ipp-attribute-fidelity', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'document-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-name', $value, \obray\ipp\enums\Types::NAME, 255, $this->naturalLanguageOverride);
                break;
            case 'compression':
                $this->attributes[$name] = new \obray\ipp\Attribute('compression', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'document-format':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-format', $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            case 'document-natural-language':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-natural-language', $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'job-k-octets':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-k-octets', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-impressions':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-impressions', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-media-sheets':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-media-sheets', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'which-jobs':
                $this->attributes[$name] = new \obray\ipp\Attribute('which-jobs', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'limit':
                $this->attributes[$name] = new \obray\ipp\Attribute('limit', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'my-jobs':
                $this->attributes[$name] = new \obray\ipp\Attribute('my-jobs', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'requested-attributes':
                $this->attributes[$name] = $this->createAttributeInstances('requested-attributes', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            default:
                throw new \Exception("Invalid operational parameter.");
        }
    }

    public function validate(array $attributeKeys)
    {
        $charset = null;
        if (isset($this->attributes['attributes-charset'])) {
            $charset = $this->attributes['attributes-charset']->getAttributeValue();
        }

        if(empty($charset) || strtolower((string) $charset) !== 'utf-8'){
            throw new ClientErrorCharsetNotSupported();
        }

        $naturalLanguage = null;
        if (isset($this->attributes['attributes-natural-language'])) {
            $naturalLanguage = $this->attributes['attributes-natural-language']->getAttributeValue();
        }

        if (trim((string) $naturalLanguage) === '') {
            throw new InvalidRequest('"attributes-natural-language" is required.');
        }
    }
}
