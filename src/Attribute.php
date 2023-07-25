<?php
namespace obray\ipp;

class Attribute implements \JsonSerializable
{
    protected $name;
    private $valueTag;
    private $nameLength;
    private $valueLength;
    private $value;
    private $offset;
    private $previousNameKey;
    public $attributes = [];
    
    public function __construct($name=NULL, $value=NULL, int $type=NULL, int $maxLength=NULL, string $naturalLanguage=NULL)
    {
        if($name===NULL) return $this;
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($name);

        if($value===NULL){
            return $this;
        }
        
        $nameToSwitchOn = $this->name->getValue();
        
        if(empty($nameToSwitchOn)){
            $nameToSwitchOn = $this->previousNameKey;
        }
        $this->value = \obray\ipp\enums\Types::getType($type, $value, $naturalLanguage, $maxLength, $nameToSwitchOn);
        
        $this->valueTag = $this->value->getValueTag();
        $this->valueLength = new \obray\ipp\types\basic\SignedShort($this->value->getLength());
    }
    
    public function getAttributeValue()
    {
        return $this->value->getValue();
    }

    public function getAttributeValueClass()
    {
        return $this->value;
    }

    public function encode()
    {
        $binary = pack('c',$this->valueTag);
        $binary .= $this->nameLength->encode();
        $binary .= $this->name->encode();
        $binary .= $this->valueLength->encode();
        $binary .= $this->value->encode();
        return $binary;
    }

    public function decode($binary, $offset=0, $debugExit=0)
    {
        if(!empty($this->nameLength) && $this->nameLength->getValue()!==0){
            $this->previousNameKey = $this->name->getValue();
        }
        
        // unpack the attribute value tag
        $this->valueTag = (unpack('cValueTag', $binary, $offset))['ValueTag'];
        $offset += 1;
        //print_r("Value Tag: " . dechex($this->valueTag) . "\n");
        
        // decode the name length and adjust offset
        $this->nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->nameLength->len();
        //print_r("Name Length: " . $this->nameLength . "\n");
        
        // decode the attribute name and adjust offset
        $this->name = (new \obray\ipp\types\basic\LocalizedString(NULL))->decode($binary, $offset, $this->nameLength->getValue());
        $offset += $this->name->len();        
        //print_r($this->name . "\n");
        
        // decode the value length and adjust offset
        $this->valueLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->valueLength->len();
        //print_r("Value Length: " . $this->valueLength->getValue() . "\n");

        $this->value = \obray\ipp\enums\Types::getType($this->valueTag, NULL, "en-us", NULL, !empty((string)$this->name)?(string)$this->name:(string)$this->previousNameKey);
        $this->value->decode($binary, $offset, $this->valueLength->getValue());
        //print_r($this->value);
        //print_r("\n");
        $offset += $this->valueLength->getValue();

        // set offset for retreival of next attribute
        $this->offset = $offset;

        return $this;
    }

    public function getName()
    {
        return $this->name->__toString();
    }

    public function getNameLength()
    {
        return $this->nameLength->getValue();
    }

    public function getOffset()
    {
        return $this->offset;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value->__toString();
    }

}