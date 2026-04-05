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
            default:
                throw new \Exception("Invalid attribute ".$name.".");
                break;
        }
    }
}
