<?php
namespace obray\ipp;

class PrinterDescriptionAttributes implements \JsonSerializable
{
    private $attribute_group_tag = 0x04;
    public $attributes;

    public function __SET(string $name, $value)
    {
        $this->$name = $value;
    }

    public function decode($binary, &$offset=8)
    {	
        $AttributeGroupTag = (unpack("cAttributeGroupTag", $binary, $offset))['AttributeGroupTag'];
        if( $AttributeGroupTag !== $this->attribute_group_tag ){ return false; }
        $validAttributeGroupTags = [0x01,0x02,0x03,0x04,0x05,0x06,0x07];
        $endOfAttributesTag = 0x03;
        $offset += 1;
        while(true){
            $attribute = (new \obray\ipp\Attribute(!empty($attributeName)?$attributeName:NULL))->decode($binary, $offset);
            if( $attribute->getNameLength() === 0 && !is_array($this->attributes[$attributeName]) ){
                $this->attributes[$attributeName] = array( 0 => $this->attributes[$attributeName] );
                $this->attributes[$attributeName][] = $attribute;
            } else if ($attribute->getNameLength() === 0 && is_array($this->attributes[$attributeName])){
                $this->attributes[$attributeName][] = $attribute;
            } else {
                $attributeName = $attribute->getName();
                $this->attributes[$attributeName] = $attribute;
            }
            $offset = $attribute->getOffset();
            $newTag = (unpack("cAttributeGroupTag", $binary, $offset))['AttributeGroupTag'];
            if($newTag===$endOfAttributesTag){
                //print_r("end of attributes - break\n");
                return false;
            }
            if(in_array($newTag,$validAttributeGroupTags)){
                //print_r("Found new valid attribute tag.\n");
                return $newTag;
            }       
        }
        
    }

    public function jsonSerialize()
    {
        return $this->attributes;
    }

}