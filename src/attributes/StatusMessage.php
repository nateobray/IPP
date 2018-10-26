<?php
namespace obray\ipp\attributes;

class StatusMessage extends obray\attributes\Attribute
{
    protected $name = 'status-message';

    public function __construct(string $value, string $naturalLanguage=NULL)
    {
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