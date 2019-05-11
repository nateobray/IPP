<?php
namespace obray\ipp;

class PrinterAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x04;

    public function set(string $name, $value){
        $this->attributes[$name] = $value;
    }
}