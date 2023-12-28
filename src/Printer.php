<?php 

namespace obray\ipp;

class Printer
{
    private $ipp;
    private $printerURI;
    private $user;
    private $password;
    private $curlOptions = [];
    
    private $lastRequest;
    private $lastResponse;

    public function __construct(string $uri, string $user='', string $password='', array $curlOptions = [])
    {
        $this->printerURI = $uri;
        $this->user = $user;
        $this->password = $password;
        $this->curlOptions = $curlOptions;
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

    public function printJob(string $document, int $requestId=1, array $attributes=NULL)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        if(!empty($attributes['document-format'])){
            $operationAttributes->{'document-format'} = $attributes['document-format'];
            unset($attributes['document-format']);
        }

        $jobAttributes = NULL;
        if(!empty($attributes)){
            $jobAttributes = new \obray\ipp\JobAttributes($attributes);
        }

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::PRINT_JOB),
            new \obray\ipp\types\Integer($requestId),
            new \obray\ipp\types\OctetString($document),
            $operationAttributes,
            $jobAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
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
        throw new \Exception("Print URI is not implemented.");
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
     * @param int $requestId    Client request id
     * @param array $attributes An array of parameters to pass to the method
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function validateJob(int $requestId=1, array $attributes=NULL)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        if(!empty($attributes['document-format'])){
        	$operationAttributes->{'document-format'} = $attributes['document-format'];
        }
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::VALIDATE_JOB),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
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
        throw new \Exception("Create Job is not implemented.");
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
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function getPrinterAttributes(int $requestId=1)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('2.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
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
     * 
     * @param int $requestId        Client request id
     * @param string $whichJobs     one of 'not-completed' or 'completed'
     * @param int $limit            max number of jobs to return
     * @param bool $myJobs          if true, only return jobs from the authenticated user
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function getJobs(int $requestId = 1, string $whichJobs = null, int $limit = null, bool $myJobs = null)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = (string)$this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        if(!empty($whichJobs)) $operationAttributes->{'which-jobs'} = (string)$whichJobs;
        if(!empty($limit)) $operationAttributes->{'limit'} = (int)$limit;
        if(!empty($myJobs)) $operationAttributes->{'my-jobs'} = (bool)$myJobs;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('2.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::GET_JOBS),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );

        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
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
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function pausePrinter(int $requestId=1)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::PAUSE_PRINTER),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
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
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function resumePrinter(int $requestId=1)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::RESUME_PRINTER),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
    }

    /**
     * Purge Printer
     * 
     * RFC 2911 3.2.9:
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
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function purgeJobs(int $requestId=1)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'requesting-user-name'} = $this->user;

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::PURGE_JOBS),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
    }   
}