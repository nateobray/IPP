<?php
namespace obray\ipp\text;

class Text extends \obray\ipp\attributes\Attribute
{
    const MaxLength = 1024;
    
    public function __construct($name, $value, $naturalLanguage=NULL, $maxLength=1024)
    {
        $this->name = $name;
        // enforce max length
        if(strlen($value)>255){
            $value = substr($value,0,255);
        }
        // encode proper string if natural language in provided
        if(!empty($naturalLanguage)){
            $value = \obray\ipp\types\TextWithLangauge($naturalLanguage, $value);
        } else {
            $value = \obray\ipp\types\TextWithoutLangauge($value);
        }
        // construct basic attribute type
        parent::__construct($value);
    }
}