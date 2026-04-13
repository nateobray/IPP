<?php

namespace obray\ipp;

class SubscriptionAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x06;

    public function set(string $name, $value): void
    {
        switch ($name) {
            case 'notify-subscription-id':
            case 'notify-time-interval':
            case 'notify-lease-duration':
            case 'notify-sequence-number':
            case 'notify-lease-expiration-time':
            case 'notify-printer-up-time':
            case 'notify-job-id':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'notify-recipient-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'notify-pull-method':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'notify-events':
            case 'notify-attributes':
                $this->attributes[$name] = $this->createAttributeInstances($name, $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'notify-user-data':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::OCTETSTRING);
                break;
            case 'notify-status-code':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'notify-charset':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'notify-natural-language':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'notify-text-format':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            default:
                if (is_int($value)) {
                    $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                } elseif (is_string($value)) {
                    $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::KEYWORD);
                } else {
                    throw new \Exception("Unsupported subscription attribute: '$name'");
                }
        }
    }
}
