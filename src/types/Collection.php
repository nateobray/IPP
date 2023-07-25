<?php
namespace obray\ipp\types;

use JsonSerializable;
use obray\ipp\Attribute;
use obray\ipp\AttributeGroup;

class Collection implements JsonSerializable
{
    protected $valueTag = 0x34;
    protected $endTag = 0x37;
    protected $length = 0;
    protected $attributes;
    protected $currentKey = null;
    protected $offset;
    
    public function set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __set(string $name, $value)
    {
        $this->set($name, $value);
    }

    public function __get(string $name)
    {
        if(!empty($this->attributes->$name)){
            return $this->attributes->$name;
        }
        throw new \Exception("Invalid attribute ".$name.".");
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function encode()
    {
        $binary = '';
        forEach($this->attributes as $key => $attribute){
            $binary .= pack('c', 0x4a);
            
            // name length
            $binary .= (new \obray\ipp\types\basic\SignedShort(0))->encode();
            
            // value length
            $binary .= (new \obray\ipp\types\basic\SignedShort(len($key)))->encode();

            // value length
            $binary .= (new \obray\ipp\types\basic\LocalizedString($key))->encode();

            $binary .= $attribute->encode();
        }
        
        $binary .= pack('c', $this->endValueTag);
        return $binary;
    }
    
    public function decode($binary, &$offset=8)
    {	
        $this->attributes = new \stdClass();
        //print_r("\n\n********* COLLECTION START **********\n\n");

        $valueTag = (unpack('cValueTag', $binary, $offset))['ValueTag'];
        //print_r("Value Tag: " . dechex($valueTag) . "\n");
        $previousNameKey = null;
        while($valueTag !== 0x37){
            
             // unpack the attribute value tag
            $valueTag = (unpack('cValueTag', $binary, $offset))['ValueTag'];
            $offset += 1;
            //print_r("Value Tag: " . dechex($valueTag) . "\n");
            
            // decode the name length and adjust offset
            $nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
            $offset += $nameLength->len();
            //print_r("Name Length: " . $nameLength . "\n");
            
            // decode the attribute name and adjust offset
            $name = (new \obray\ipp\types\basic\LocalizedString(NULL))->decode($binary, $offset, $nameLength->getValue());
            $offset += $name->len();        
            //print_r($name . "\n");
            
            // decode the value length and adjust offset
            $valueLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
            $offset += $valueLength->len();
            //print_r("Value Length: " . $valueLength->getValue() . "\n");

            if($valueTag === 0x4a){
                $value = (new \obray\ipp\types\basic\LocalizedString(NULL));
                $value->decode($binary, $offset, $valueLength->getValue());
                $this->currentKey = (string)$value;
            } else {
                $value = \obray\ipp\enums\Types::getType($valueTag, NULL, "en-us", NULL, !empty((string)$name)?(string)$name:(string)$previousNameKey);
                $value->decode($binary, $offset, $valueLength->getValue());

                if(!empty($nameLength) && $nameLength->getValue() !== 0){
                    $previousNameKey = $this->name->getValue();
                }
            }
            
            $offset += $valueLength->getValue();

            if($valueTag === $this->endTag){
                //print_r("end value tag 0x37\n");
                continue;
            } 

            if($valueTag !== 0x4A) $this->attributes->{$this->currentKey} = $value;
        }

    }

    public function jsonSerialize(): mixed
    {
        //print_r((object)$this->attributes);
        //exit();
        return (object)$this->attributes;
    }
}