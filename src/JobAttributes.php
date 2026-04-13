<?php

namespace obray\ipp;

class JobAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x02;

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

    /**
     * __construct
     *
     * Allows pre-setting the JobAttributes from an associative array of attributes in the form name => value.
     *
     * For example to preset page-ranges to the first 2 pages only:
     *
     * $attributeArray = [
     *  'page-ranges' => '1-2'
     * ]
     *
     * @param array|null $attributeArray
     */
    public function __construct(?array $attributeArray = null)
    {

        if (is_array($attributeArray)) {

            foreach ($attributeArray as $name => $value) {

                try {
                    $this->set($name,$value);
                } catch (\Exception $e) {
                    // Skip any invalid attributes when instantiating from an array
                    continue;
                }

            }

        }

    }


    /**
     * Set
     * Defines the attributes that can be set on the Job Attribute Group.
     */
    public function set(string $name, $value)
    {
        switch($name) {
            case 'attributes-charset':
                $this->attributes[$name] = new \obray\ipp\Attribute('attributes-charset', $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'attributes-natural-language':
                $this->attributes[$name] = new \obray\ipp\Attribute('attributes-natural-language', $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'date-time-at-completed':
                $this->attributes[$name] = new \obray\ipp\Attribute('date-time-at-completed', $value, \obray\ipp\enums\Types::DATETIME);
                break;
            case 'date-time-at-creation':
                $this->attributes[$name] = new \obray\ipp\Attribute('date-time-at-creation', $value, \obray\ipp\enums\Types::DATETIME);
                break;
            case 'date-time-at-processing':
                $this->attributes[$name] = new \obray\ipp\Attribute('date-time-at-processing', $value, \obray\ipp\enums\Types::DATETIME);
                break;
            case 'document-format':
                $this->attributes[$name] = new \obray\ipp\Attribute('document-format', $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            case 'job-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-uri', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'job-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-id', $value, \obray\ipp\enums\Types::INTEGER);
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
            case 'job-impressions-completed':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-impressions-completed', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-state':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-state', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'job-state-reasons':
                $this->attributes[$name] = $this->createAttributeInstances('job-state-reasons', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-state-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-state-message', (string)$value, \obray\ipp\enums\Types::TEXT);
                break;
            case 'job-detailed-status-messages':
                $this->attributes[$name] = $this->createAttributeInstances('job-detailed-status-messages', $value, \obray\ipp\enums\Types::TEXT);
                break;
            case 'job-document-access-errors':
                $this->attributes[$name] = $this->createAttributeInstances('job-document-access-errors', $value, \obray\ipp\enums\Types::TEXT);
                break;
            case 'job-k-octets-processed':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-k-octets-processed', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-media-sheets-completed':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-media-sheets-completed', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-more-info':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-more-info', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'job-message-from-operator':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-message-from-operator', $value, \obray\ipp\enums\Types::TEXT, 127);
                break;
            case 'job-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-name', $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'job-originating-user-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-originating-user-name', $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'job-printer-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-printer-uri', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'job-printer-up-time':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-printer-up-time', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'number-of-intervening-jobs':
                $this->attributes[$name] = new \obray\ipp\Attribute('number-of-intervening-jobs', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'number-of-documents':
                $this->attributes[$name] = new \obray\ipp\Attribute('number-of-documents', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'output-device-assigned':
                $this->attributes[$name] = new \obray\ipp\Attribute('output-device-assigned', $value, \obray\ipp\enums\Types::NAME, 127);
                break;
            case 'job-priority':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-priority', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-hold-until':
                $this->attributes[$name] = $this->createKeywordOrNameAttribute('job-hold-until', $value);
                break;
            case 'job-sheets':
                $this->attributes[$name] = $this->createKeywordOrNameAttribute('job-sheets', $value);
                break;
            case 'multiple-document-handling':
                $this->attributes[$name] = new \obray\ipp\Attribute('multiple-document-handling', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'copies':
                $this->attributes[$name] = new \obray\ipp\Attribute('copies', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'finishings':
                $this->attributes[$name] = $this->createAttributeInstances('finishings', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'page-ranges':
                $this->attributes[$name] = $this->createAttributeInstances('page-ranges', $value, \obray\ipp\enums\Types::RANGEOFINTEGER);
                break;
            case 'sides':
                $this->attributes[$name] = new \obray\ipp\Attribute('sides', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'number-up':
                $this->attributes[$name] = new \obray\ipp\Attribute('number-up', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'orientation-requested':
                $this->attributes[$name] = new \obray\ipp\Attribute('orientation-requested', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'media':
                $this->attributes[$name] = $this->createKeywordOrNameAttribute('media', $value);
                break;
            case 'printer-resolution':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-resolution', $value, \obray\ipp\enums\Types::RESOLUTION);
                break;
            case 'print-quality':
                $this->attributes[$name] = new \obray\ipp\Attribute('print-quality', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'print-scaling':
                $this->attributes[$name] = new \obray\ipp\Attribute('print-scaling', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'time-at-completed':
                $this->attributes[$name] = new \obray\ipp\Attribute('time-at-completed', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'time-at-creation':
                $this->attributes[$name] = new \obray\ipp\Attribute('time-at-creation', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'time-at-processing':
                $this->attributes[$name] = new \obray\ipp\Attribute('time-at-processing', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'media-col':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('media-col', $value);
                break;
            case 'output-bin':
                $this->attributes[$name] = new \obray\ipp\Attribute('output-bin', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            // PWG5100.3 — Production Printing Attributes
            case 'job-account-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-account-id', $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'job-accounting-user-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-accounting-user-id', $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'job-sheet-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-sheet-message', $value, \obray\ipp\enums\Types::TEXT, 255);
                break;
            case 'output-device':
                $this->attributes[$name] = new \obray\ipp\Attribute('output-device', $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'page-delivery':
                $this->attributes[$name] = new \obray\ipp\Attribute('page-delivery', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'page-order-received':
                $this->attributes[$name] = new \obray\ipp\Attribute('page-order-received', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'presentation-direction-number-up':
                $this->attributes[$name] = new \obray\ipp\Attribute('presentation-direction-number-up', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'separator-sheets':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('separator-sheets', $value);
                break;
            case 'insert-sheet':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('insert-sheet', $value);
                break;
            // PWG5100.6 — Page Overrides
            case 'overrides':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('overrides', $value);
                break;
            // PWG5100.7 — IPP Job Extensions v2.0
            case 'sheet-collate':
                $this->attributes[$name] = new \obray\ipp\Attribute('sheet-collate', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-error-action':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-error-action', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-mandatory-attributes':
                $this->attributes[$name] = $this->createAttributeInstances('job-mandatory-attributes', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-recipient-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-recipient-name', $value, \obray\ipp\enums\Types::NAME, 127);
                break;
            case 'imposition-template':
                $this->attributes[$name] = $this->createKeywordOrNameAttribute('imposition-template', $value);
                break;
            case 'cover-back':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('cover-back', $value);
                break;
            case 'cover-front':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('cover-front', $value);
                break;
            case 'job-error-sheet':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-error-sheet', $value);
                break;
            case 'proof-print':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('proof-print', $value);
                break;
            case 'job-save-disposition':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-save-disposition', $value);
                break;
            case 'job-message-to-operator':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-message-to-operator', $value, \obray\ipp\enums\Types::TEXT, 255);
                break;
            // PWG5100.11 — Job and Printer Extensions Set 2
            case 'job-accounting-sheets':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-accounting-sheets', $value);
                break;
            case 'job-cover-back':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-cover-back', $value);
                break;
            case 'job-cover-front':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('job-cover-front', $value);
                break;
            case 'job-finished-state-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-finished-state-message', $value, \obray\ipp\enums\Types::TEXT, 255);
                break;
            default:
                $this->attributes[$name] = $this->buildGenericAttribute($name, $value);
                break;
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

        throw new \Exception("Invalid attribute ".$name.".");
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
