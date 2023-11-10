<?php

namespace obray\ipp;

use obray\ipp\types\RangeOfInteger;

class JobAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x02;

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
                } catch (\Exception) {
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
            case 'job-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-uri', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'job-id':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-id', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-state':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-state', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'job-state':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-state-reasons', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-state-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-state-message', (string)$value, \obray\ipp\enums\Types::TEXT);
                break;
            case 'number-of-intervening-jobs':
                $this->attributes[$name] = new \obray\ipp\Attribute('number-of-intervening-jobs', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-priority':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-priority', $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'job-hold-until':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-hold-until', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'job-sheets':
                $this->attributes[$name] = new \obray\ipp\Attribute('job-sheets', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'multiple-document-handling':
                $this->attributes[$name] = new \obray\ipp\Attribute('multiple-document-handling', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'copies':
                $this->attributes[$name] = new \obray\ipp\Attribute('copies', $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'finishings':
                $this->attributes[$name] = new \obray\ipp\Attribute('finishings', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'page-ranges':
                $this->attributes[$name] = new \obray\ipp\Attribute('page-ranges', $value, \obray\ipp\enums\Types::RANGEOFINTEGER);
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
                $this->attributes[$name] = new \obray\ipp\Attribute('media', $value, \obray\ipp\enums\Types::KEYWORD);
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
            case 'media-col':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute('media-col', $value);
                break;
            default:
                throw new \Exception("Invalid attribute ".$name.".");
                break;
        }
    }
}