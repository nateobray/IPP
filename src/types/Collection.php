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

    public function __construct(?array $attributes = null)
    {
        $this->attributes = [];

        if ($attributes === null) {
            return;
        }

        foreach ($attributes as $name => $value) {
            if (is_array($value) && array_key_exists('value', $value) && array_key_exists('type', $value)) {
                $type = $this->normalizeType($value['type']);
                $value = \obray\ipp\enums\Types::getType($type, $value['value']);
            } else if (is_array($value)) {
                $value = new self($value);
            } else if (is_bool($value)) {
                $value = new \obray\ipp\types\Boolean($value);
            } else if (is_int($value)) {
                $value = new \obray\ipp\types\Integer($value);
            } else {
                $value = new \obray\ipp\types\Keyword((string) $value);
            }

            $this->attributes[$name] = $value;
        }
    }

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
        if(!empty($this->attributes[$name])){
            return $this->attributes[$name];
        }
        throw new \Exception("Invalid attribute ".$name.".");
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getValueTag()
    {
        return $this->valueTag;
    }

    public function getLength()
    {
        return strlen($this->encode());
    }

    public function encode()
    {
        $binary = '';
        forEach($this->attributes as $key => $attribute){
            $binary .= pack('c', 0x4a);
            
            // name length
            $binary .= (new \obray\ipp\types\basic\SignedShort(0))->encode();
            
            // value length
            $binary .= (new \obray\ipp\types\basic\SignedShort(strlen($key)))->encode();

            // value length
            $binary .= (new \obray\ipp\types\basic\LocalizedString($key))->encode();

            $binary .= $attribute->encode();
        }
        
        $binary .= pack('c', $this->endTag);
        return $binary;
    }
    
    public function decode($binary, &$offset=8)
    {	
        $this->attributes = [];
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
                    $previousNameKey = $name->getValue();
                }
            }
            
            $offset += $valueLength->getValue();

            if($valueTag === $this->endTag){
                //print_r("end value tag 0x37\n");
                continue;
            } 

            if($valueTag !== 0x4A) $this->attributes[$this->currentKey] = $value;
        }

    }

    public function jsonSerialize(): mixed
    {
        //print_r((object)$this->attributes);
        //exit();
        return (object)$this->attributes;
    }

    private function normalizeType($type): ?int
    {
        if (is_int($type)) {
            return $type;
        }

        if (!is_string($type)) {
            return NULL;
        }

        $lookup = [
            'boolean' => \obray\ipp\enums\Types::BOOLEAN,
            'integer' => \obray\ipp\enums\Types::INTEGER,
            'keyword' => \obray\ipp\enums\Types::KEYWORD,
            'name' => \obray\ipp\enums\Types::NAME,
            'text' => \obray\ipp\enums\Types::TEXT,
            'uri' => \obray\ipp\enums\Types::URI,
            'collection' => \obray\ipp\enums\Types::COLLECTION,
        ];

        $type = strtolower(trim($type));
        return $lookup[$type] ?? NULL;
    }
}