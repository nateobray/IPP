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
            case 'job-printer-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-printer-uri', $value, \obray\ipp\enums\Types::URI, 1023);
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
            case 'my-subscriptions':
                $this->attributes[$name] = new \obray\ipp\Attribute('my-subscriptions', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'notify-subscription-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('notify-subscription-id', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'notify-job-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('notify-job-id', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'document-number':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-number', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'notify-lease-duration':
                $this->attributes[$name] = new \obray\ipp\Attribute('notify-lease-duration', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'notify-subscription-ids':
                $this->attributes[$name] = $this->createAttributeInstances($name, (array) $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'notify-sequence-numbers':
                $this->attributes[$name] = $this->createAttributeInstances($name, (array) $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'notify-wait':
                $this->attributes[$name] = new \obray\ipp\Attribute('notify-wait', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'requested-attributes':
                $this->attributes[$name] = $this->createAttributeInstances('requested-attributes', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            default:
                $this->attributes[$name] = $this->buildGenericAttribute($name, $value);
        }
    }

    private function buildGenericAttribute(string $name, $value)
    {
        if (is_array($value) && array_key_exists('value', $value)) {
            $type = $this->normalizeType($value['type'] ?? NULL);
            $attributeValue = $value['value'];

            if ($type !== NULL) {
                return new \obray\ipp\Attribute($name, $attributeValue, $type);
            }
        }

        if (is_bool($value)) {
            return new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::BOOLEAN);
        }

        if (is_int($value)) {
            return new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
        }

        if (is_string($value)) {
            return new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::KEYWORD);
        }

        throw new \Exception("Invalid operational parameter: '" . $name . "' is not a supported operation attribute.");
    }

    private function normalizeType($type): ?int
    {
        if (is_int($type)) {
            return $type;
        }

        if (!is_string($type)) {
            return NULL;
        }

        $lookup = [
            'boolean'         => \obray\ipp\enums\Types::BOOLEAN,
            'integer'         => \obray\ipp\enums\Types::INTEGER,
            'enum'            => \obray\ipp\enums\Types::ENUM,
            'keyword'         => \obray\ipp\enums\Types::KEYWORD,
            'mime'            => \obray\ipp\enums\Types::MIMEMEDIATYPE,
            'mime-media-type' => \obray\ipp\enums\Types::MIMEMEDIATYPE,
            'name'            => \obray\ipp\enums\Types::NAME,
            'text'            => \obray\ipp\enums\Types::TEXT,
            'uri'             => \obray\ipp\enums\Types::URI,
            'charset'         => \obray\ipp\enums\Types::CHARSET,
            'natural-language'=> \obray\ipp\enums\Types::NATURALLANGUAGE,
        ];

        $type = strtolower(trim($type));
        return $lookup[$type] ?? NULL;
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
