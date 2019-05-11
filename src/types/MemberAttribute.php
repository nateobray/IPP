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

    public function __construct(string $name, $value, \obray\ipp\enums\Types $type=NULL, $natuarlLanguage=NULL, $maxLength=NULL)
    {
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

    /**
     * Encode
     * 
     * Encoded according to RFC8010 secion 3.1.7
     */

    public function encode()
    {
        print_r("Member encode!!!\n");
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

    public function decode()
    {

    }

    public function jsonSerialize()
    {
        return $this->memberValue;
    }
}