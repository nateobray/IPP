<?php 

namespace obray;

/*
 * Operation Attributes
 * 
 * @property string $charset            This operation attribute identifies the charset (coded
                                        character set and encoding method) used by any ’text’ and
                                        ’name’ attributes that the client is supplying in this request
 * @property string $naturalLanguage    This operation attribute identifies the natural language used 
 *                                      by any ’text’ and ’name’ attributes that the client is supplying 
 *                                      in this request.
 * @property string $statusCode         
 * @property string $statusMessage
 * @property string $detailedStatusMessage
 * @property string $documentURI
 * @property string $target
 * @property string $userName
 * @property string $jobName
 * @property string $ippAttributeFidelity
 * @property string $documentName
 * @property string $compression
 * @peoperty string $documentType
 * @property string $naturalLanguage
 * @property string $jobKOctets
 * @property string $jobImpressions
 * @property string $jobMediaSheets
*/

class OperationAttributes
{
    public $charset = 'utf-8';
    public $naturalLanguage = 'en';
    public $statusCode;
    public $statusMessage;
    public $detailedStatusMessage;
    public $documentAccessError;
    public $documentURI;
    public $target;
    public $userName;
    public $jobName;
    public $ippAttributeFidelity;
    public $documentName;
    public $compression;
    public $documentType;
    public $jobKOctets;
    public $jobImpressions;
    public $jobMediaSheets;

    public function validate(array $attributeKeys)
    {
        if(empty($charset) || $charset !== 'utf-8'){
            throw new obray\exceptions\ClientErrorCharsetNotSupported();
        }
        if(empty($natuarlLanguage) && $naturalLanguage !== 'en'){
            throw new \Exception("Invalid request.");
        }
    }
}