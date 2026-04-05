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
    
    public function __construct($name = null, $value = null, ?int $type = null, ?int $maxLength = null, ?string $naturalLanguage = null)
    {
        if($name===NULL) return $this;
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($name);

        if($value===NULL){
            return $this;
        }

        if ($value instanceof \obray\ipp\interfaces\TypeInterface) {
            $this->value = $value;
            $this->valueTag = $this->value->getValueTag();
            $this->valueLength = new \obray\ipp\types\basic\SignedShort($this->value->getLength());

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

    public function withOmittedName(): self
    {
        $attribute = clone $this;
        $attribute->nameLength = new \obray\ipp\types\basic\SignedShort(0);
        $attribute->name = new \obray\ipp\types\basic\LocalizedString('');

        return $attribute;
    }

    public function decode($binary, $offset=0, $debugExit=0)
    {
        if(!empty($this->nameLength) && $this->nameLength->getValue()!==0){
            $this->previousNameKey = $this->name->getValue();
        }
        
        $this->valueTag = \obray\ipp\transport\DecodeGuard::readByte($binary, $offset, 'attribute value tag');
        $offset += 1;
        
        $this->nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        if ($this->nameLength->getValue() < 0) {
            throw new \UnexpectedValueException('Encountered a negative attribute name length.');
        }
        $offset += $this->nameLength->len();
        
        $this->name = (new \obray\ipp\types\basic\LocalizedString(NULL))->decode($binary, $offset, $this->nameLength->getValue());
        $offset += $this->name->len();        

        if ($this->nameLength->getValue() === 0 && empty((string) $this->previousNameKey)) {
            throw new \UnexpectedValueException('Encountered an attribute with an omitted name before any attribute name was decoded.');
        }
        
        $this->valueLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        if ($this->valueLength->getValue() < 0) {
            throw new \UnexpectedValueException('Encountered a negative attribute value length.');
        }
        $offset += $this->valueLength->len();

        $this->value = \obray\ipp\enums\Types::getType($this->valueTag, NULL, "en-us", NULL, !empty((string)$this->name)?(string)$this->name:(string)$this->previousNameKey);
        $this->value->decode($binary, $offset, $this->valueLength->getValue());
        $offset += $this->valueLength->getValue();

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
