<?php
namespace obray\ipp\types;

class NameWithLanguage extends \obray\ipp\types\basic\OctetString
{
    protected $valueTag = 0x36;
    private $language;
    private $languageSize;
    private $name;
    private $nameSize;

    public function __construct(string $naturalLanguage=NULL, string $string=NULL)
    {
        if($naturalLanguage===NULL || $string===NULL) return $this;
        $this->language = new \obray\ipp\types\basic\OctetString($naturalLanguage);
        $this->languageSize = new \obray\ipp\types\basic\SignedShort($this->language->getLength());
        $this->name = new \obray\ipp\types\basic\OctetString($string);
        $this->nameSize = new \obray\ipp\types\basic\SignedShort($this->name->getLength());
    }

    public function encode()
    {
        return $this->languageSize->encode() . $this->language->encode() . $this->nameSize->encode() . $this->name->encode();
    }

    public function decode($binary, $offset=0, $length=NULL)
    {
        $this->languageSize = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset);
        $this->language = (new \obray\ipp\types\basic\Octetstring())->decode($binary, $offset + $this->languageSize->getLength(), $this->languageSize->getValue());
        $this->nameSize = (new \obray\ipp\types\basic\SignedShort())->decode($binary, $offset + $this->languageSize->getLength() + $this->language->getLength());
        $this->name = (new \obray\ipp\types\basic\Octetstring())->decode($binary, $offset + $this->languageSize->getLength() + $this->language->getLength() + $this->nameSize->getLength() , $this->nameSize->getValue());
        return $this;
    }

    public function getValue()
    {
        return (string)$this;
    }

    public function getLength(): int
    {
        return ($this->languageSize->getLength() 
               + $this->language->getLength() 
               + $this->nameSize->getLength() 
               + $this->name->getLength());
    }

    public function __toString()
    {
        return (string)$this->name;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (string)$this->name->getValue();
    }
}
