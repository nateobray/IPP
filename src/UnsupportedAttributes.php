<?php

class UnsupportedAttributes extends AttributeGroup
{
    protected $attribute_group_tag = 0x05;

    public function set(string $name, $value)
    {
        $this->attributes[$name] = $value;
    }
}