<?php
namespace obray\ipp;

class PrinterAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x04;

    public function set(string $name, $value){
        switch($name){
            case 'charset-configured':
                $this->attributes[$name] = new \obray\ipp\Attribute('charset-configured', $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'charset-supported':
                $this->attributes[$name] = $this->createAttributeInstances('charset-supported', $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'color-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('color-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'compression-supported':
                $this->attributes[$name] = $this->createAttributeInstances('compression-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'device-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('device-uri', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'document-format-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-format-default', $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            case 'document-format-supported':
                $this->attributes[$name] = $this->createAttributeInstances('document-format-supported', $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            case 'media-supported':
                $this->attributes[$name] = $this->createAttributeInstances('media-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'generated-natural-language-supported':
                $this->attributes[$name] = $this->createAttributeInstances('generated-natural-language-supported', $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'job-k-octets-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-k-octets-supported', $value, \obray\ipp\enums\Types::RANGEOFINTEGER);
                break;
            case 'job-impressions-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-impressions-supported', $value, \obray\ipp\enums\Types::RANGEOFINTEGER);
                break;
            case 'job-media-sheets-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-media-sheets-supported', $value, \obray\ipp\enums\Types::RANGEOFINTEGER);
                break;
            case 'ipp-versions-supported':
                $this->attributes[$name] = $this->createAttributeInstances('ipp-versions-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'multiple-document-jobs-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('multiple-document-jobs-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'natural-language-configured':
                $this->attributes[$name] = new \obray\ipp\Attribute('natural-language-configured', $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'operations-supported':
                $this->attributes[$name] = $this->createAttributeInstances('operations-supported', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'pdl-override-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('pdl-override-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'port-monitor':
                $this->attributes[$name] = new \obray\ipp\Attribute('port-monitor', $value, \obray\ipp\enums\Types::NAME, 127);
                break;
            case 'ppd-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('ppd-name', $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'printer-message-from-operator':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-message-from-operator', $value, \obray\ipp\enums\Types::TEXT, 127);
                break;
            case 'printer-resolution-supported':
                $this->attributes[$name] = $this->createAttributeInstances('printer-resolution-supported', $value, \obray\ipp\enums\Types::RESOLUTION);
                break;
            case 'printer-make-and-model':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-make-and-model', $value, \obray\ipp\enums\Types::TEXT, 127);
                break;
            case 'printer-is-accepting-jobs':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-is-accepting-jobs', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'printer-info':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-info', $value, \obray\ipp\enums\Types::TEXT, 127);
                break;
            case 'printer-location':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-location', $value, \obray\ipp\enums\Types::TEXT, 127);
                break;
            case 'printer-more-info':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-more-info', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'printer-driver-installer':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-driver-installer', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'printer-more-info-manufacturer':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-more-info-manufacturer', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'printer-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-name', $value, \obray\ipp\enums\Types::NAME, 127);
                break;
            case 'printer-state':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-state', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'printer-state-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-state-message', $value, \obray\ipp\enums\Types::TEXT);
                break;
            case 'printer-state-reasons':
                $this->attributes[$name] = $this->createAttributeInstances('printer-state-reasons', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'printer-up-time':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-up-time', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'printer-current-time':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-current-time', $value, \obray\ipp\enums\Types::DATETIME);
                break;
            case 'printer-uri-supported':
                $this->attributes[$name] = $this->createAttributeInstances('printer-uri-supported', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'queued-job-count':
                $this->attributes[$name] = new \obray\ipp\Attribute('queued-job-count', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'multiple-operation-time-out':
                $this->attributes[$name] = new \obray\ipp\Attribute('multiple-operation-time-out', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'pages-per-minute':
                $this->attributes[$name] = new \obray\ipp\Attribute('pages-per-minute', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'pages-per-minute-color':
                $this->attributes[$name] = new \obray\ipp\Attribute('pages-per-minute-color', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'reference-uri-schemes-supported':
                $this->attributes[$name] = $this->createAttributeInstances('reference-uri-schemes-supported', $value, \obray\ipp\enums\Types::URISCHEME);
                break;
            case 'uri-authentication-supported':
                $this->attributes[$name] = $this->createAttributeInstances('uri-authentication-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'uri-security-supported':
                $this->attributes[$name] = $this->createAttributeInstances('uri-security-supported', $value, \obray\ipp\enums\Types::KEYWORD);
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

            if ($type === \obray\ipp\enums\Types::COLLECTION && is_array($attributeValue)) {
                return new \obray\ipp\CollectionAttribute($name, $attributeValue);
            }

            if ($type !== NULL) {
                return new \obray\ipp\Attribute($name, $attributeValue, $type);
            }
        }

        if (is_array($value)) {
            return new \obray\ipp\CollectionAttribute($name, $value);
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

        throw new \Exception("Invalid operational parameter.");
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
            'boolean' => \obray\ipp\enums\Types::BOOLEAN,
            'integer' => \obray\ipp\enums\Types::INTEGER,
            'enum' => \obray\ipp\enums\Types::ENUM,
            'keyword' => \obray\ipp\enums\Types::KEYWORD,
            'mime' => \obray\ipp\enums\Types::MIMEMEDIATYPE,
            'mime-media-type' => \obray\ipp\enums\Types::MIMEMEDIATYPE,
            'name' => \obray\ipp\enums\Types::NAME,
            'range' => \obray\ipp\enums\Types::RANGEOFINTEGER,
            'range-of-integer' => \obray\ipp\enums\Types::RANGEOFINTEGER,
            'resolution' => \obray\ipp\enums\Types::RESOLUTION,
            'text' => \obray\ipp\enums\Types::TEXT,
            'uri' => \obray\ipp\enums\Types::URI,
            'collection' => \obray\ipp\enums\Types::COLLECTION,
        ];

        $type = strtolower(trim($type));
        return $lookup[$type] ?? NULL;
    }
}
