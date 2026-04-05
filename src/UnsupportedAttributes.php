<?php

namespace obray\ipp;

class UnsupportedAttributes extends AttributeGroup
{
    protected $attribute_group_tag = 0x05;

    public function set(string $name, $value)
    {
        if (is_array($value)) {
            $attributes = [];
            $isFirstValue = true;
            foreach ($value as $attribute) {
                if (!$attribute instanceof \obray\ipp\Attribute) {
                    throw new \InvalidArgumentException('Unsupported attribute arrays must contain Attribute instances.');
                }

                $attributes[] = $isFirstValue ? $attribute : $attribute->withOmittedName();
                $isFirstValue = false;
            }

            $this->attributes[$name] = $attributes;

            return;
        }

        if ($value instanceof \obray\ipp\Attribute) {
            $this->attributes[$name] = $value;

            return;
        }

        $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::UNKNOWN);
    }
}
