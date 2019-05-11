<?php
namespace obray\ipp;

class CollectionAttribute implements \JsonSerializable
{
    // tags
    private $valueTag = 0x34;
    private $endValueTag = 0x37;
    private $endNameLength = 0x0000;
    private $endValueLength = 0x0000;

    // names and values
    private $nameLength = 0;
    private $name = "";
    private $valueLength = 0;
    
    private $members = [];

    public function __construct($name=NULL, array $members)
    {
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($name);
        $this->valueLength = new \obray\ipp\types\basic\SignedShort(0);

        forEach($members as $name => $value){
            $this->members[] = new \obray\ipp\types\MemberAttribute($name, $value);
        }
        
        $this->endNameLength = new \obray\ipp\types\basic\SignedShort(0);
        $this->endValueLength = new \obray\ipp\types\basic\SignedShort(0);
        
    }

    public function getValue(string $name, $value)
    {
        return $value;
    }

    public function encode()
    {
        /**
         * 34                           - value tag
         * 00 09                        - name length (media-col)
         * 6d 65 64 69 61 2d 63 6f 6c   - name 9 bytes
         * 00 00                        - value length
         * 
         * -- Member attribute encoding --
         * 4a0000000c6d656469612d736f757263654400000008656e76656c6f7065
         * 
         * 37                           - end value tag
         * 00 00                        - end name length
         * 00 00                        - end value length
         */
        
        print_r($this->members);
        $binary = pack('c', $this->valueTag);
        $binary .= $this->nameLength->encode();
        $binary .= $this->name->encode();
        $binary .= $this->valueLength->encode();
        forEach($this->members as $member){
            $binary .= $member->encode();
        }
        $binary .= pack('c', $this->endValueTag);
        $binary .= $this->endNameLength->encode();
        $binary .= $this->endValueLength->encode();

        return $binary;        
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        
    }

    public function jsonSerialize()
    {
        return $this->value;
    }
}