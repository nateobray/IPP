<?php
namespace obray\ipp\types;

class MemberAttribute implements \JsonSerializable
{
    private $valueTag = 0x4a;
    private $nameLength = 0x0000;
    private $valueLength = 0;
    private $value = "";
    private $memberValueTag;
    private $memberValueLength = 0;
    private $memberValue;

    public function __construct(string $name=NULL, $value=NULL, \obray\ipp\enums\Types $type=NULL, $natuarlLanguage=NULL, $maxLength=NULL)
    {
        if($name === NULL && $value === NULL) return $this;
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(0);
        // member key
        $this->valueLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->value = new \obray\ipp\types\NameWithoutLanguage($name);

        // determine type if not specified
        if($type===NULL){
            if(is_integer($value)){
                $type = \obray\ipp\enums\Types::INTEGER;
            } else if (is_string($value)){
                $type = \obray\ipp\enums\Types::KEYWORD;
            }
        }
        
        // member value
        $this->memberValueTag = $type;
        $this->memberValue = \obray\ipp\enums\Types::getType($type, $value, $natuarlLanguage, $maxLength);
        $this->memberValueLength = new \obray\ipp\types\basic\SignedShort($this->memberValue->getLength());
    }

    public function getKey()
    {
        return $this->value->getValue();
    }

    public function getValue()
    {
        return $this->memberValue->getValue();
    }

    public function getLength()
    {
        return 1 + $this->nameLength->getLength() + $this->valueLength->getLength() + $this->value->getLength() + 1 + $this->nameLength->getLength()  + $this->memberValueLength->getLength() + $this->memberValueLength->getValue();
    }

    /**
     * Encode
     * 
     * Encoded according to RFC8010 secion 3.1.7
     */

    public function encode()
    {
        /**
         * 4a                                   - value tag
         * 00 00                                - name length
         * 00 0c                                - value length
         * 6d 65 64 69 61 2d 73 6f 75 72 63 65  - media-source (12 bytes)
         * 44                                   - member value tag
         * 00 00                                - name length
         * 00 08                                - member-value-length
         * 65 6e 76 65 6c 6f 70 65              - member value (8 bytes)  
         */
        // value tag
        $binary = pack('c', $this->valueTag);
        // name-length 0x0000
        $binary .= $this->nameLength->encode();
        // value-length (value is w)
        $binary .= $this->valueLength->encode();
        // value (member-name)
        $binary .= $this->value->encode();
        // member-value-tag
        $binary .= pack('c', $this->memberValueTag);
        // name-length (value is 0x0000)
        $binary .= $this->nameLength->encode();
        // member-value-length (value is x)
        $binary .= $this->memberValueLength->encode();
        // member-value
        $binary .= $this->memberValue->encode();
        return $binary;
    }

    /**
     * Decode
     * 
     * Decode according to RFC8010 secion 3.1.7
     */

    public function decode($binary, $offset=0, $length=NULL)
    {
        // get value tag
        $this->valueTag = unpack('cTag', $binary, $offset)['Tag'];
        $offset += 1;
        // get name length
        $this->nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->nameLength->getLength();
        // get value length
        $this->valueLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->valueLength->getLength();
        // get value (in this case the value is the key)
        $this->value = (new \obray\ipp\types\NameWithoutLanguage())->decode($binary, $offset, $this->valueLength->getValue());
        $offset += $this->value->getLength();
        // get value tag
        $this->memberValueTag = unpack('cTag', $binary, $offset)['Tag'];
        $offset += 1;
        // get name length
        $this->nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->nameLength->getLength();
        // member value
        $this->memberValueLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->memberValueLength->getLength();
        // get the correct value type and decode
        $this->memberValue = \obray\ipp\enums\Types::getType($this->memberValueTag);
        $this->memberValue->decode($binary, $offset, $this->memberValueLength->getValue());
        $offset += $this->valueLength->getValue();
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->memberValue;
    }
}