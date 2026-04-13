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
            // PWG5107.2 — IEEE 1284 Device ID
            case 'printer-device-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-device-id', $value, \obray\ipp\enums\Types::TEXT, 1023);
                break;
            case 'device-service-count':
                $this->attributes[$name] = new \obray\ipp\Attribute('device-service-count', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'device-uuid':
                $this->attributes[$name] = new \obray\ipp\Attribute('device-uuid', $value, \obray\ipp\enums\Types::URI);
                break;
            // PWG5100.9 — Printer State Extensions
            case 'printer-uuid':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-uuid', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'printer-state-change-date-time':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-state-change-date-time', $value, \obray\ipp\enums\Types::DATETIME);
                break;
            case 'printer-state-change-time':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-state-change-time', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'printer-config-change-date-time':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-config-change-date-time', $value, \obray\ipp\enums\Types::DATETIME);
                break;
            case 'printer-config-change-time':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-config-change-time', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'printer-supply':
                $this->attributes[$name] = $this->createAttributeInstances('printer-supply', $value, \obray\ipp\enums\Types::OCTETSTRING);
                break;
            case 'printer-supply-description':
                $this->attributes[$name] = $this->createAttributeInstances('printer-supply-description', $value, \obray\ipp\enums\Types::TEXT, 255);
                break;
            case 'printer-supply-info-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-supply-info-uri', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'printer-alert':
                $this->attributes[$name] = $this->createAttributeInstances('printer-alert', $value, \obray\ipp\enums\Types::OCTETSTRING);
                break;
            case 'printer-alert-description':
                $this->attributes[$name] = $this->createAttributeInstances('printer-alert-description', $value, \obray\ipp\enums\Types::TEXT, 255);
                break;
            case 'printer-input-tray':
                $this->attributes[$name] = $this->createAttributeInstances('printer-input-tray', $value, \obray\ipp\enums\Types::OCTETSTRING);
                break;
            case 'printer-output-tray':
                $this->attributes[$name] = $this->createAttributeInstances('printer-output-tray', $value, \obray\ipp\enums\Types::OCTETSTRING);
                break;
            case 'job-settable-attributes-supported':
                $this->attributes[$name] = $this->createAttributeInstances('job-settable-attributes-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'printer-settable-attributes-supported':
                $this->attributes[$name] = $this->createAttributeInstances('printer-settable-attributes-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            // PWG5100.2 — output-bin
            case 'output-bin-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('output-bin-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'output-bin-supported':
                $this->attributes[$name] = $this->createAttributeInstances('output-bin-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            // PWG5101.1 — media description attributes
            case 'media-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('media-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'media-ready':
                $this->attributes[$name] = $this->createAttributeInstances('media-ready', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'media-size-supported':
                $this->attributes[$name] = $this->createAttributeInstances('media-size-supported', $value, \obray\ipp\enums\Types::COLLECTION);
                break;
            case 'media-col-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('media-col-default', $value);
                break;
            case 'media-col-database':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('media-col-database', $value);
                break;
            case 'media-col-ready':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('media-col-ready', $value);
                break;
            // PWG5100.3 — Production Printing Attributes
            case 'job-account-id-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-account-id-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'job-accounting-user-id-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-accounting-user-id-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'job-sheet-message-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-sheet-message-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'multiple-document-handling-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('multiple-document-handling-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'multiple-document-handling-supported':
                $this->attributes[$name] = $this->createAttributeInstances('multiple-document-handling-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'output-device-supported':
                $this->attributes[$name] = $this->createAttributeInstances('output-device-supported', $value, \obray\ipp\enums\Types::NAME, 127);
                break;
            case 'page-delivery-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('page-delivery-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'page-delivery-supported':
                $this->attributes[$name] = $this->createAttributeInstances('page-delivery-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'page-order-received-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('page-order-received-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'page-order-received-supported':
                $this->attributes[$name] = $this->createAttributeInstances('page-order-received-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'presentation-direction-number-up-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('presentation-direction-number-up-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'presentation-direction-number-up-supported':
                $this->attributes[$name] = $this->createAttributeInstances('presentation-direction-number-up-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'separator-sheets-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('separator-sheets-default', $value);
                break;
            case 'separator-sheets-supported':
                $this->attributes[$name] = $this->createAttributeInstances('separator-sheets-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'insert-sheet-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('insert-sheet-default', $value);
                break;
            case 'insert-sheet-supported':
                $this->attributes[$name] = $this->createAttributeInstances('insert-sheet-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            // PWG5100.7 — IPP Job Extensions v2.0
            case 'sheet-collate-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('sheet-collate-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'sheet-collate-supported':
                $this->attributes[$name] = $this->createAttributeInstances('sheet-collate-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-error-action-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-error-action-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-error-action-supported':
                $this->attributes[$name] = $this->createAttributeInstances('job-error-action-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-error-sheet-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-error-sheet-default', $value);
                break;
            case 'job-error-sheet-supported':
                $this->attributes[$name] = $this->createAttributeInstances('job-error-sheet-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-mandatory-attributes-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-mandatory-attributes-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'job-message-to-operator-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-message-to-operator-default', $value, \obray\ipp\enums\Types::TEXT, 255);
                break;
            case 'job-message-to-operator-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-message-to-operator-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'job-recipient-name-supported':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-recipient-name-supported', $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'job-save-disposition-supported':
                $this->attributes[$name] = $this->createAttributeInstances('job-save-disposition-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'proof-print-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('proof-print-default', $value);
                break;
            case 'proof-print-supported':
                $this->attributes[$name] = $this->createAttributeInstances('proof-print-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'cover-back-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('cover-back-default', $value);
                break;
            case 'cover-back-supported':
                $this->attributes[$name] = $this->createAttributeInstances('cover-back-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'cover-front-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('cover-front-default', $value);
                break;
            case 'cover-front-supported':
                $this->attributes[$name] = $this->createAttributeInstances('cover-front-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'imposition-template-default':
                $this->attributes[$name] = new \obray\ipp\Attribute('imposition-template-default', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'imposition-template-supported':
                $this->attributes[$name] = $this->createAttributeInstances('imposition-template-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            // PWG5100.11 — Job and Printer Extensions Set 2
            case 'ipp-features-supported':
                $this->attributes[$name] = $this->createAttributeInstances('ipp-features-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'printer-get-attributes-supported':
                $this->attributes[$name] = $this->createAttributeInstances('printer-get-attributes-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-accounting-sheets-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-accounting-sheets-default', $value);
                break;
            case 'job-accounting-sheets-supported':
                $this->attributes[$name] = $this->createAttributeInstances('job-accounting-sheets-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-cover-back-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-cover-back-default', $value);
                break;
            case 'job-cover-back-supported':
                $this->attributes[$name] = $this->createAttributeInstances('job-cover-back-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-cover-front-default':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-cover-front-default', $value);
                break;
            case 'job-cover-front-supported':
                $this->attributes[$name] = $this->createAttributeInstances('job-cover-front-supported', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            // PWG5100.1 — finishings description attributes
            case 'finishings-default':
                $this->attributes[$name] = $this->createAttributeInstances('finishings-default', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'finishings-supported':
                $this->attributes[$name] = $this->createAttributeInstances('finishings-supported', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'finishings-ready':
                $this->attributes[$name] = $this->createAttributeInstances('finishings-ready', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'finishings-col-supported':
                $this->attributes[$name] = $this->createAttributeInstances('finishings-col-supported', $value, \obray\ipp\enums\Types::KEYWORD);
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
