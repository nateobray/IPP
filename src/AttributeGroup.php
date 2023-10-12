<?php
namespace obray\ipp;

abstract class AttributeGroup implements \JsonSerializable
{
    protected $attribute_group_tag;
    protected $attributes = array();

    /**
     * Abstract Method Set
     * 
     * Concrete classes of the attribute group class must define the set of valid
     * attributes and the associated types.
     */

    abstract protected function set(string $name, $value);

    /**
     * __set Override
     * Used to allow setting attributes using the syntax:
     * 
     * $attributeGroup->{attribute name} = {value};
     * 
     * @param string $name - the name of the attribute to be set
     * @param mixed $value - the value to be set
     */

    public function __set(string $name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * __get Override
     * Returns the value from the attributes array.  This allows value retreival with 
     * syntax like:
     * 
     * $myAttribute = $attributeGroup->{attribute name};
     * 
     * @param string $name - the name of the attribute to retreive
     */

    public function __get(string $name)
    {
        if(!empty($this->attributes[$name])){
            return $this->attributes[$name];
        }
        throw new \Exception("Invalid attribute ".$name.".");
    }

    /**
     * Encode
     * 
     * Encodes the attribute group according to RFC8010 secion 3.1.2
     */

    public function encode()
    {
        $binary = pack('c',$this->attribute_group_tag);
        forEach($this->attributes as $name => $attribute){
            $binary .= $attribute->encode();
        }
        return $binary;
    }

    /**
     * Decode
     * 
     * Decodes the attribute group according to RFC8010 secion 3.1.2
     */

    public function decode($binary, &$offset=8)
    {	
        $AttributeGroupTag = (unpack("cAttributeGroupTag", $binary, $offset))['AttributeGroupTag'];
        if( $AttributeGroupTag !== $this->attribute_group_tag ){ return false; }
        $validAttributeGroupTags = [0x01,0x02,0x03,0x04,0x05,0x06,0x07,0x08,0x09,0x0a,0x0b,0x0c,0x0d,0x0e,0x0f];
        $endOfAttributesTag = 0x03;
        $offset += 1;
        return $this->decodeAttributes($binary, $offset, $validAttributeGroupTags, $endOfAttributesTag, $this->attributes);
    }

    protected function decodeAttributes($binary, &$offset, $validAttributeGroupTags, $endOfAttributesTag, &$attributes)
    {
        $offset = 8;
        while(true){
            
            $attribute = (new \obray\ipp\Attribute(!empty($attributeName)?$attributeName:NULL))->decode($binary, $offset);
            
            if( !empty($attributeName) && $attribute->getNameLength() === 0 && !is_array($attributes[$attributeName]) ){
                $attributes[$attributeName] = array( 0 => $attributes[$attributeName] );
                $attributes[$attributeName][] = $attribute;
            } else if ( !empty($attributeName) && $attribute->getNameLength() === 0 && is_array($attributes[$attributeName])){
                $attributes[$attributeName][] = $attribute;
            } else {
                $attributeName = $attribute->getName();
                $attributes[$attributeName] = $attribute;
            }
            
            $offset = $attribute->getOffset();
            if($offset === strlen($binary)) return false;
            
            $newTag = (unpack("cAttributeGroupTag", $binary, $offset))['AttributeGroupTag'];
            if($newTag===$endOfAttributesTag){
                return false;
            }

            if(in_array($newTag, $validAttributeGroupTags)){
                return $newTag;
            }       
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->attributes;
    }
}
