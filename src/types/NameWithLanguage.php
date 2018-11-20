<?php
namespace obray\ipp\types;

class NameWithLanguage extends \obray\ipp\types\basic\OctetString
{
    protected $valueTag = 0x36;

    public function __construct(string $naturalLanguage, string $string)
    {
        $this->naturalLanguage = $naturalLanguage;
        $this->naturalLanguageOctets = strlen($naturalLanguage);
        $this->string = $string;
        $this->stringOctets = strlen($this->string);
    }

    public function encode()
    {
        $naturalLanguageBinary;
        forEach(str_split($this->naturalLanguage) as $char){
            $naturalLanguageBinary .= unpack('c',$char);
        }

        $stringBinary;
        forEach(str_split($this->string) as $char){
            $stringBinary .= unpack('c',$char);
        }

        return unpack('n',$this->naturalLanguageOctets) . $naturalLanguageBinary . unpack('n',$this->stringOctets) . $stringBinary;
    }
}