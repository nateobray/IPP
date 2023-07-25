<?php
namespace obray\ipp;

class Job
{
    private $ipp;
    private $printerURI;
    private $jobID;
    private $user;
    private $password;
    private $curlOptions = [];
    private $request;
    
    private $lastRequest;
    private $lastResponse;

    public function __construct($uri, $jobID, $user=null, $password=NULL, $curlOptions = [])
    {
        $this->printerURI = $uri;
        $this->jobID = $jobID;
        $this->user = $user;
        $this->password = $password;
        $this->curlOptions = $curlOptions;
        $this->request = \obray\ipp\Request::class;
    }

    /**
     * Send Document
     * 
     * RFC 2911 3.3.1:
     * This OPTIONAL operation allows a client to create a multi-document
     * Job object that is initially "empty" (contains no documents).  In the
     * Create-Job response, the Printer object returns the Job object's URI
     * (the "job-uri" attribute) and the Job object's 32-bit identifier (the
     * "job-id" attribute).  For each new document that the client desires
     * to add, the client uses a Send-Document operation.  Each Send-
     * Document Request contains the entire stream of document data for one
     * document.
     * 
     * If the Printer supports this operation but does not support multiple
     * documents per job, the Printer MUST reject subsequent Send-Document
     * operations supplied with data and return the 'server-error-multiple-
     * document-jobs-not-supported'.  However, the Printer MUST accept the
     * first document with a 'true' or 'false' value for the "last-document"
     * operation attribute (see below), so that clients MAY always submit
     * one document jobs with a 'false' value for "last-document" in the
     * first Send-Document and a 'true' for "last-document" in the second
     * Send-Document (with no data).
     * 
     * Since the Create-Job and the send operations (Send-Document or Send-
     * URI operations) that follow could occur over an arbitrarily long
     * period of time for a particular job, a client MUST send another send
     * operation within an IPP Printer defined minimum time interval after
     * the receipt of the previous request for the job.  If a Printer object
     * supports the Create-Job and Send-Document operations, the Printer
     * object MUST support the "multiple-operation-time-out" attribute (see
     * section 4.4.31).  This attribute indicates the minimum number of
     * seconds the Printer object will wait for the next send operation
     * before taking some recovery action.
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function sendDocument()
    {
        throw new \Exception("Send document not implemented.");
    }

    /**
     * Send URI
     * 
     * RFC 2911 3.3.2:
     * This OPTIONAL operation is identical to the Send-Document operation
     * (see section 3.3.1) except that a client MUST supply a URI reference
     * ("document-uri" operation attribute) rather than the document data
     * itself.  If a Printer object supports this operation, clients can use
     * both Send-URI or Send-Document operations to add new documents to an
     * existing multi-document Job object.  However, if a client needs to
     * indicate that the previous Send-URI or Send-Document was the last
     * document,  the client MUST use the Send-Document operation with no
     * document data and the "last-document" flag set to 'true' (rather than
     * using a Send-URI operation with no "document-uri" operation
     * attribute).
     * 
     * If a Printer object supports this operation, it MUST also support the
     * Print-URI operation (see section 3.2.2).
     * 
     * The Printer object MUST validate the syntax and URI scheme of the
     * supplied URI before returning a response, just as in the Print-URI
     * operation.  The IPP Printer MAY validate the accessibility of the
     * document as part of the operation or subsequently (see section
     * 3.2.2).
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function sendURI()
    {
        throw new \Exception("Send URI not implemented.");
    }

    /**
     * Cancel Job
     * 
     * RFC 2911 3.3.3:
     * This REQUIRED operation allows a client to cancel a Print Job from
     * the time the job is created up to the time it is completed, canceled,
     * or aborted.  Since a Job might already be printing by the time a
     * Cancel-Job is received, some media sheet pages might be printed
     * before the job is actually terminated.
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function cancelJob(int $requestId=0)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'job-id'} = $this->jobID;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::CANCEL_JOB),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
    }

    /**
     * Get Job Attributes
     * 
     * RFC 2911 3.3.4:
     * This REQUIRED operation allows a client to request the values of
     * attributes of a Job object and it is almost identical to the Get-
     * Printer-Attributes operation (see section 3.2.5).  The only
     * differences are that the operation is directed at a Job object rather
     * than a Printer object, there is no "document-format" operation
     * attribute used when querying a Job object, and the returned attribute
     * group is a set of Job object attributes rather than a set of Printer
     * object attributes.
     * 
     * For Jobs, the possible names of attribute groups are:
     * - 'job-template': the subset of the Job Template attributes that
     *   apply to a Job object (the first column of the table in Section
     *   4.2) that the implementation supports for Job objects.
     * - 'job-description': the subset of the Job Description attributes
     *   specified in Section 4.3 that the implementation supports for
     *   Job objects.
     * - 'all': the special group 'all' that includes all attributes that
     *   the implementation supports for Job objects.
     * 
     * Since a client MAY request specific attributes or named groups, there
     * is a potential that there is some overlap.  For example, if a client
     * requests, 'job-name' and 'job-description', the client is actually
     * requesting the "job-name" attribute once by naming it explicitly, and
     * once by inclusion in the 'job-description' group.  In such cases, the
     * Printer object NEED NOT return the attribute only once in the
     * response even if it is requested multiple times.  The client SHOULD
     * NOT request the same attribute in multiple ways.
     * 
     * It is NOT REQUIRED that a Job object support all attributes belonging
     * to a group (since some attributes are OPTIONAL).  However it is
     * REQUIRED that each Job object support all these group names.
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function getJobAttributes()
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'job-id'} = $this->jobID;
        if(!empty($this->user)){
            $operationAttributes->{'requesting-user-name'} = $this->user;
        }
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::GET_JOB_ATTRIBUTES),
            new \obray\ipp\types\Integer(123456),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
    }

    /**
     * Hold Job
     * 
     * RFC 2911 3.3.5:
     * This OPTIONAL operation allows a client to hold a pending job in the
     * queue so that it is not eligible for scheduling.  If the Hold-Job
     * operation is supported, then the Release-Job operation MUST be
     * supported, and vice-versa.  The OPTIONAL "job-hold-until" operation
     * attribute allows a client to specify whether to hold the job
     * indefinitely or until a specified time period, if supported.
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function holdJob(int $requestId=0)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'job-id'} = $this->jobID;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::HOLD_JOB),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
    }

    /**
     * Release Job
     * 
     * RFC 2911 3.3.6:
     * This OPTIONAL operation allows a client to release a previously held
     * job so that it is again eligible for scheduling.  If the Hold-Job
     * operation is supported, then the Release-Job operation MUST be
     * supported, and vice-versa.
     * 
     * This operation removes the "job-hold-until" job attribute, if
     * present, from the job object that had been supplied in the create or
     * most recent Hold-Job or Restart-Job operation and removes its effect
     * on the job.  The IPP object MUST remove the 'job-hold-until-
     * specified' value from the job's "job-state-reasons" attribute, if
     * present.  See section 4.3.8.
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function releaseJob(int $requestId=0)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'job-id'} = $this->jobID;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::RELEASE_JOB),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
    }

    /**
     * Restart Job
     * 
     * RFC 2911 3.3.7:
     * This OPTIONAL operation allows a client to restart a job that is
     * retained in the queue after processing has completed (see section
     * 4.3.7.2).
     * 
     * The job is moved to the 'pending' or 'pending-held' job state and
     * restarts at the beginning on the same IPP Printer object with the
     * same attribute values.  If any of the documents in the job were
     * passed by reference (Print-URI or Send-URI), the Printer MUST re-
     * fetch the data, since the semantics of Restart-Job are to repeat all
     * Job processing.  The Job Description attributes that accumulate job
     * progress, such as "job-impressions-completed", "job-media-sheets-
     * completed", and "job-k-octets-processed", MUST be reset to 0 so that
     * they give an accurate record of the job from its restart point.  The
     * job object MUST continue to use the same "job-uri" and "job-id"
     * attribute values.
     * 
     * @param int $requestId    Client request id
     * 
     * @return \obray\ipp\transport\IPPPayload
     */

    public function restartJob(int $requestId=0)
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'job-id'} = $this->jobID;
        $operationAttributes->{'requesting-user-name'} = $this->user;
        
        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::RESTART_JOB),
            new \obray\ipp\types\Integer($requestId),
            NULL,
            $operationAttributes
        );
        $encodedPayload = $payload->encode();
        return \obray\ipp\Request::send($this->printerURI, $encodedPayload, $this->user, $this->password, $this->curlOptions);
    }

}