<?php 

namespace obray\ipp;

class Printer
{
    private $ipp;
    private $printerURI;
    private $user;
    private $password;
    
    private $lastRequest;
    private $lastResponse;

    public function __construct($uri, $user, $password=NULL)
    {
        $this->printerURI = $uri;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Print Job
     * 
     * RFC 2911 3.2.1:
     * This REQUIRED operation allows a client to submit a print job with
     * only one document and supply the document data (rather than just a
     * reference to the data).  See Section 15 for the suggested steps for
     * processing create operations and their Operation and Job Template
     * attributes.
     *
     * @param string $document A document to be printed
     * @param array $attributes Optional attributes to be sent to the printer
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function printJob(string $document, array $attributes=NULL)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        if(!empty($attributes['document-format'])){
        	$operationAttributes->{'document-format'} = $attributes['document-format'];
        }
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::printJob),
            new \obray\ipp\types\Integer(123456),
            new \obray\ipp\types\OctetString($document),
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    /**
     * Print URI (optional implementation - skipping for now)
     * 
     * RFC 2911 3.2.2:
     * This OPTIONAL operation is identical to the Print-Job operation
     * (section 3.2.1) except that a client supplies a URI reference to the
     * document data using the "document-uri" (uri) operation attribute (in
     * Group 1) rather than including the document data itself.  Before
     * returning the response, the Printer MUST validate that the Printer
     * supports the retrieval method (e.g., http, ftp, etc.) implied by the
     * URI, and MUST check for valid URI syntax.  If the client-supplied URI
     * scheme is not supported, i.e. the value is not in the Printer
     * object’s "referenced-uri-scheme-supported" attribute, the Printer
     * object MUST reject the request and return the ’client-error-uri-
     * scheme-not-supported’ status code.
     */

    public function printURI(string $documentURI, array $attributes)
    {

    }

    /**
     * Validate Job
     * 
     * RFC 2911 3.2.3:
     * This REQUIRED operation is similar to the Print-Job operation
     * (section 3.2.1) except that a client supplies no document data and
     * the Printer allocates no resources (i.e., it does not create a new
     * Job object).  This operation is used only to verify capabilities of a
     * printer object against whatever attributes are supplied by the client
     * in the Validate-Job request.  By using the Validate-Job operation a
     * client can validate that an identical Print-Job operation (with the
     * document data) would be accepted. The Validate-Job operation also
     * performs the same security negotiation as the Print-Job operation
     * (see section 8), so that a client can check that the client and
     * Printer object security requirements can be met before performing a
     * Print-Job operation.
     *
     * @param array $attributes An array of parameters to pass to the method
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function validateJob(array $attributes=NULL)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        if(!empty($attributes['document-format'])){
        	$operationAttributes->{'document-format'} = $attributes['document-format'];
        }
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::validateJob),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    /**
     * Create Job
     * 
     * RFC 2911 3.2.4:
     * This OPTIONAL operation is similar to the Print-Job operation
     * (section 3.2.1) except that in the Create-Job request, a client does
     * not supply document data or any reference to document data.  Also,
     * the client does not supply any of the "document-name", "document-
     * format", "compression", or "document-natural-language" operation
     * attributes.  This operation is followed by one or more Send-Document
     * or Send-URI operations.  In each of those operation requests, the
     * client OPTIONALLY supplies the "document-name", "document-format",
     * and "document-natural-language" attributes for each document in the
     * multi-document Job object.
     */

    public function createJob()
    {
        
    }

    /**
     * Get Printer Attributes
     * 
     * RFC 2911 3.2.5:
     * This REQUIRED operation allows a client to request the values of the
     * attributes of a Printer object.   In the request, the client supplies
     * the set of Printer attribute names and/or attribute group names in
     * which the requester is interested.  In the response, the Printer
     * object returns a corresponding attribute set with the appropriate
     * attribute values filled in.
     */

    public function getPrinterAttributes()
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::getPrinterAttributes),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    /**
     * Get Jobs
     * 
     * RFC 2911 3.2.6:
     * This REQUIRED operation allows a client to retrieve the list of Job
     * objects belonging to the target Printer object.  The client may also
     * supply a list of Job attribute names and/or attribute group names.  A
     * group of Job object attributes will be returned for each Job object
     * that is returned.
     */

    public function getJobs()
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::getJobs),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    /**
     * Pause Printer
     * 
     * RFC 2911 3.2.7:
     * This OPTIONAL operation allows a client to stop the Printer object
     * from scheduling jobs on all its devices.  Depending on
     * implementation, the Pause-Printer operation MAY also stop the Printer
     * from processing the current job or jobs.  Any job that is currently
     * being printed is either stopped as soon as the implementation permits
     */

    public function pausePrinter()
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::pausePrinter),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    /**
     * Resume Printer
     * 
     * RFC 2911 3.2.8:
     * This operation allows a client to resume the Printer object
     * scheduling jobs on all its devices.  The Printer object MUST remove
     * the ’paused’ and ’moving-to-paused’ values from the Printer object’s
     * "printer-state-reasons" attribute, if present.  If there are no other
     * reasons to keep a device paused (such as media-jam), the IPP Printer
     * is free to transition itself to the ’processing’ or ’idle’ states,
     * depending on whether there are jobs to be processed or not,
     * respectively, and the device(s) resume processing jobs.
     * 
     * If the Pause-Printer operation is supported, then the Resume-Printer
     * operation MUST be supported, and vice-versa.
     */

    public function resumePrinter()
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::resumePrinter),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    /**
     * Purge Printer
     * 
     * RFC 2911 3.2.8:
     * This OPTIONAL operation allows a client to remove all jobs from an
     * IPP Printer object, regardless of their job states, including jobs in
     * the Printer object’s Job History (see Section 4.3.7.2).  After a
     * Purge-Jobs operation has been performed, a Printer object MUST return
     * no jobs in subsequent Get-Job-Attributes and Get-Jobs responses
     * (until new jobs are submitted).
     * 
     * Whether the Purge-Jobs (and Get-Jobs) operation affects jobs that
     * were submitted to the device from other sources than the IPP Printer
     * object in the same way that the Purge-Jobs operation affects jobs
     * that were submitted to the IPP Printer object using IPP, depends on
     * implementation, i.e., on whether the IPP protocol is being used as a
     * universal management protocol or just to manage IPP jobs,
     * respectively.
     */

    public function purgeJobs(\obray\OperationAttributes $operationAttributes)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::purgeJobs),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return $this->send($encodedPayload);
    }

    /**
     * send
     * 
     * This method applies request headers, formulates the request and then
     * parses the response into a response payload.
     * 
     * @params string $encodedPayload This is the actual payload of the request
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    private function send(string $encodedPayload)
    {
        $headers = array("Content-Type" => "application/ipp");
        if(!empty($this->user) && !empty($this->password)){
            $headers["Authorization"] = 'Basic ' . base64_encode($this->user.':'.$this->password);
        }

        $http = new \obray\HTTP();
        $http->addRequest($this->printerURI.'?waitjob=false&waitprinter=false', \obray\HTTP::POST, $encodedPayload, $headers);
        $this->lastRequest = ($http->getRequests())[0];
        $this->lastResponse = ($http->send())[0];
        
        $responsePayload = new \obray\ipp\transport\IPPPayload();
        $responsePayload->decode($this->lastResponse->getBody());
        return $responsePayload;
    }
}