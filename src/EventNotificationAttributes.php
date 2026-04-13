<?php

namespace obray\ipp;

class EventNotificationAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x07;

    public function set(string $name, $value): void
    {
        switch ($name) {
            case 'notify-subscription-id':
            case 'notify-sequence-number':
            case 'notify-job-id':
            case 'printer-up-time':
            case 'job-impressions-completed':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'notify-subscribed-event':
            case 'printer-state-reasons':
            case 'job-state-reasons':
                $this->attributes[$name] = $this->createAttributeInstances($name, $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'notify-printer-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'printer-name':
            case 'job-name':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'notify-text':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::TEXT, 1023);
                break;
            case 'notify-charset':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'notify-natural-language':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'printer-state':
            case 'job-state':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'printer-is-accepting-jobs':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            default:
                if (is_int($value)) {
                    $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                } elseif (is_string($value)) {
                    $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::KEYWORD);
                } else {
                    throw new \Exception("Unsupported event notification attribute: '$name'");
                }
        }
    }
}
