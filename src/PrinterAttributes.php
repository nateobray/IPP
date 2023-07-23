<?php
namespace obray\ipp;

class PrinterAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x04;

    public function set(string $name, $value){
        switch($name){
            case 'device-uri':
                $this->attributes[$name] = new \obray\ipp\Attribute('device-uri', $value, \obray\ipp\enums\Types::URI);
                break;
            case 'port-monitor':
                $this->attributes[$name] = new \obray\ipp\Attribute('port-monitor', $value, \obray\ipp\enums\Types::NAME, 127);
                break;
            case 'ppd-name':
                $this->attributes[$name] = new \obray\ipp\Attribute('ppd-name', $value, \obray\ipp\enums\Types::NAME, 255);
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
            case 'printer-state':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-state', $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'printer-state-message':
                $this->attributes[$name] = new \obray\ipp\Attribute('printer-state', $value, \obray\ipp\enums\Types::TEXT);
                break;
            default:
                throw new \Exception("Invalid operational parameter.");
        }
    }
}