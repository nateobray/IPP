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

    public function __construct($name=NULL, array $members=NULL)
    {
        // hardcoded values
        $this->valueLength = new \obray\ipp\types\basic\SignedShort(0);
        $this->endNameLength = new \obray\ipp\types\basic\SignedShort(0);
        $this->endValueLength = new \obray\ipp\types\basic\SignedShort(0);

        // return this if no name and value specified
        if($name === NULL && $members === NULL) return $this;

        // populate object properties
        $this->nameLength = new \obray\ipp\types\basic\SignedShort(strlen($name));
        $this->name = new \obray\ipp\types\basic\LocalizedString($name);
        $valueLength = 0;
        forEach($members as $name => $value){
            $this->members[] = new \obray\ipp\types\MemberAttribute($name, $value);
        }
    }

    private function getArray()
    {
        $memberArray = [];
        forEach( $this->members as $member ){
            $memberArray[$member->getKey()] = $member->getValue();
        }
        return $memberArray;
    }

    public function getValue()
    {
        return $this->getArray();
    }

    public function getLength()
    {
        $totalLength = 0;
        $totalLength += 1;
        $totalLength += $this->nameLength->getLength();
        $totalLength += $this->name->getLength();
        $totalLength += $this->valueLength->getLength();
        forEach($this->members as $member){
            $totalLength += $member->getLength();
        }
        $totalLength += 1;
        $totalLength += $this->endNameLength->getLength();
        $totalLength += $this->endValueLength->getLength();
        return $totalLength;
    }

    /**
     * Encode
     * 
     * Returns a binary string that represents the collection attribute
     * structure defined in RFC8010
     * 
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

    public function encode()
    {
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
    /**
     * Decode
     * 
     * Takes a binary string and from the offset decodes it until the
     * collection attribute structure according to RFC8010
     * 
     * Here's an example encoding represented in hex:
     * 
     * 34                                           - value tag
     * 00 0e                                        - name length
     * 74 65 73 74 43 6f 6c 6c 65 63 74 69 6f 6e    - name 14 bytes (testCollection)
     * 00 00                                        - value length
     * 
     * -- Mmeber attribute encoding ---
     * 4a0000000e746573744174747269627574653144000000127465737441747472696275746556616c75654a0000000e746573744174747269627574653221000000040000000a4a0000000e7465737441747472696275746533440000000b48656c6c6f20576f726c64
     * 
     * 37                                           - end value tag
     * 00 00                                        - end name length
     * 00 00                                        - end value length
     */

    public function decode($binary, $offset=0, $length=NULL)
    {
        $hex = bin2hex($binary);
        // get the tag
        $tag = unpack('cTag', $binary)['Tag'];
        $offset = 1;
        // length of the collection name
        $this->nameLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->nameLength->getLength();
        // the collection name
        $this->name = (new \obray\ipp\types\basic\LocalizedString())->decode($binary, $offset, $this->nameLength->getValue());
        $offset += $this->name->getLength();
        // the length of the collection value (all members)
        $this->valueLength = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $offset += $this->valueLength->getLength();
        
        while(true){
            $member = (new \obray\ipp\types\MemberAttribute())->decode($binary, $offset);
            $offset += $member->getLength();
            $this->members[] = $member;
            if( unpack('cEndValueTag', $binary, $offset)['EndValueTag'] === 0x37 && (unpack('n', $binary, $offset+1))[1] === 0x0000 && (unpack('n', $binary, $offset+3))[1] === 0x0000 ){
                break;
            }
        }
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getArray();
    }
}