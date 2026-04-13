<?php
namespace obray\ipp;

class Job
{
    private readonly string $printerURI;
    private readonly int|string $jobID;
    private readonly ?string $user;
    private readonly ?string $password;
    private readonly array $curlOptions;
    private string $requestClass;

    public function __construct(string $uri, int|string $jobID, ?string $user = null, ?string $password = null, array $curlOptions = [], ?string $requestClass = null)
    {
        $this->printerURI = $uri;
        $this->jobID = $jobID;
        $this->user = $user;
        $this->password = $password;
        $this->curlOptions = $curlOptions;
        $this->requestClass = $requestClass ?? \obray\ipp\Request::class;
    }

    public function setRequestClass(string $requestClass): self
    {
        $this->requestClass = $requestClass;

        return $this;
    }

    private function createOperationAttributes(?array &$attributes = null): \obray\ipp\OperationAttributes
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        if (is_string($this->jobID)) {
            $operationAttributes->{'job-uri'} = $this->jobID;
        } else {
            $operationAttributes->{'printer-uri'} = $this->printerURI;
            $operationAttributes->{'job-id'} = $this->jobID;
        }
        if (!empty($this->user)) {
            $operationAttributes->{'requesting-user-name'} = $this->user;
        }

        if (!is_array($attributes) || $attributes === []) {
            return $operationAttributes;
        }

        if (isset($attributes['attributes-natural-language'])) {
            $operationAttributes->setNaturalLanguage((string) $attributes['attributes-natural-language']);
        }

        $operationAttributeNames = [
            'attributes-charset',
            'attributes-natural-language',
            'document-format',
            'document-name',
            'document-natural-language',
            'document-number',
            'document-uri',
            'compression',
            'ipp-attribute-fidelity',
            'job-hold-until',
            'job-name',
            'job-printer-uri',
            'last-document',
            'requested-attributes',
        ];

        foreach ($operationAttributeNames as $attributeName) {
            if (!array_key_exists($attributeName, $attributes)) {
                continue;
            }

            $operationAttributes->{$attributeName} = $attributes[$attributeName];
            unset($attributes[$attributeName]);
        }

        return $operationAttributes;
    }

    private function sendPayload(\obray\ipp\transport\IPPPayload $payload): \obray\ipp\transport\IPPPayload
    {
        $requestClass = $this->requestClass;
        $targetURI = is_string($this->jobID) ? $this->jobID : $this->printerURI;

        return $requestClass::send(
            $targetURI,
            $payload->encode(),
            $this->user,
            $this->password,
            $this->curlOptions
        );
    }

    private function buildPayload(
        int $operationCode,
        int $requestId,
        ?\obray\ipp\OperationAttributes $operationAttributes,
        ?\obray\ipp\types\OctetString $document = null,
        string $versionNumber = '1.1'
    ): \obray\ipp\transport\IPPPayload {
        \obray\ipp\spec\OperationRequestValidator::validate(
            $operationCode,
            $operationAttributes,
            null,
            $document
        );

        return new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber($versionNumber),
            new \obray\ipp\types\Operation($operationCode),
            new \obray\ipp\types\Integer($requestId),
            $document,
            $operationAttributes
        );
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

    public function sendDocument(string $document = '', bool|int $lastDocument = true, int|bool $requestId = 1, ?array $attributes = null): \obray\ipp\transport\IPPPayload
    {
        if (is_int($lastDocument) && is_bool($requestId)) {
            [$lastDocument, $requestId] = [$requestId, $lastDocument];
        }

        $attributes = $attributes ?? [];
        $attributes['last-document'] = $lastDocument;

        $operationAttributes = $this->createOperationAttributes($attributes);
        $documentPayload = $document === '' ? null : new \obray\ipp\types\OctetString($document);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::SEND_DOCUMENT,
                $requestId,
                $operationAttributes,
                $documentPayload
            )
        );
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

    public function sendURI(string $documentURI, bool|int $lastDocument = true, int|bool|array $requestId = 1, ?array $attributes = null): \obray\ipp\transport\IPPPayload
    {
        if (is_array($requestId)) {
            $attributes = $requestId;
            $requestId = is_int($lastDocument) ? $lastDocument : 1;
            $lastDocument = true;
        }

        if (is_int($lastDocument) && is_bool($requestId)) {
            [$lastDocument, $requestId] = [$requestId, $lastDocument];
        }

        $attributes = $attributes ?? [];
        $attributes['document-uri'] = $documentURI;
        $attributes['last-document'] = $lastDocument;

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::SEND_URI,
                $requestId,
                $operationAttributes
            )
        );
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

    public function cancelJob(int $requestId=1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CANCEL_JOB,
                $requestId,
                $operationAttributes
            )
        );
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

    public function getJobAttributes(int $requestId=1, ?array $requestedAttributes = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_JOB_ATTRIBUTES,
                $requestId,
                $operationAttributes
            )
        );
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

    public function holdJob(int $requestId=1, ?string $jobHoldUntil = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($jobHoldUntil !== null) {
            $attributes['job-hold-until'] = $jobHoldUntil;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::HOLD_JOB,
                $requestId,
                $operationAttributes
            )
        );
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

    public function releaseJob(int $requestId=1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::RELEASE_JOB,
                $requestId,
                $operationAttributes
            )
        );
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

    public function restartJob(int $requestId=1, ?string $jobHoldUntil = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($jobHoldUntil !== null) {
            $attributes['job-hold-until'] = $jobHoldUntil;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::RESTART_JOB,
                $requestId,
                $operationAttributes
            )
        );
    }

    public function closeJob(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CLOSE_JOB,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    /**
     * CUPS Move Job
     *
     * Moves a job to a different printer queue on the same CUPS server.
     *
     * @param string $destinationPrinterURI The URI of the destination printer
     * @param int    $requestId             Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function moveJob(string $destinationPrinterURI, int $requestId=1): \obray\ipp\transport\IPPPayload
    {
        $attributes = ['job-printer-uri' => $destinationPrinterURI];
        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CUPS_MOVE_JOB,
                $requestId,
                $operationAttributes
            )
        );
    }

    /**
     * CUPS Authenticate Job
     *
     * Authenticates a held job so that it can be scheduled for printing.
     * Used when a job is held awaiting authentication.
     *
     * @param int $requestId Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function authenticateJob(int $requestId=1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CUPS_AUTHENTICATE_JOB,
                $requestId,
                $operationAttributes
            )
        );
    }

    public function cancelCurrentJob(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CANCEL_CURRENT_JOB,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    public function suspendCurrentJob(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::SUSPEND_CURRENT_JOB,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    public function resumeJob(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::RESUME_JOB,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    public function promoteJob(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::PROMOTE_JOB,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    public function reprocessJob(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::REPROCESS_JOB,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    public function scheduleJobAfter(int|string $jobAfter, int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        if (is_int($jobAfter)) {
            $operationAttributes->{'job-id-after'} = $jobAfter;
        } else {
            $operationAttributes->{'job-uri-after'} = ['value' => $jobAfter, 'type' => 'uri'];
        }

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::SCHEDULE_JOB_AFTER,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    /**
     * Create Job Subscription
     *
     * RFC 3995 §11.1.2:
     * This OPTIONAL operation creates a subscription associated with a Job
     * object. The client supplies one Subscription Template Attributes group.
     *
     * @param array $subscriptionAttributes Associative array of subscription attributes
     * @param int   $requestId              Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function createJobSubscription(array $subscriptionAttributes, int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        $subAttrs = new \obray\ipp\SubscriptionAttributes();
        foreach ($subscriptionAttributes as $name => $value) {
            $subAttrs->set($name, $value);
        }

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::CREATE_JOB_SUBSCRIPTION,
            $operationAttributes
        );

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::CREATE_JOB_SUBSCRIPTION),
            new \obray\ipp\types\Integer($requestId),
            null,
            $operationAttributes,
            null,
            null,
            null,
            $subAttrs
        );

        return $this->sendPayload($payload);
    }

    /**
     * Get Documents (PWG5100.5 §3.2.1)
     *
     * Returns a list of Document objects (and their attributes) for this job.
     *
     * @param int        $requestId           Client request id
     * @param array|null $requestedAttributes Subset of document attributes to retrieve
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function getDocuments(int $requestId = 1, ?array $requestedAttributes = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_DOCUMENTS,
                $requestId,
                $operationAttributes,
                null,
                '2.0'
            )
        );
    }

    /**
     * Create Document (PWG5100.5 §3.2.2)
     *
     * Adds a new Document object to this job. The response includes the
     * assigned document-number and document-uri. Follow up with
     * Send-Document or Send-URI to supply the actual document data.
     *
     * @param array $documentAttributes Document template attributes to set
     * @param int   $requestId          Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function createDocument(array $documentAttributes = [], int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $attributes = array_merge(['last-document' => false], $documentAttributes);
        $operationAttributes = $this->createOperationAttributes($attributes);

        $docAttrs = null;
        if (!empty($documentAttributes)) {
            $docAttrs = new \obray\ipp\DocumentAttributes();
            foreach ($documentAttributes as $name => $value) {
                // skip attributes that belong in the operation group
                if (in_array($name, ['last-document', 'document-name', 'document-format',
                    'document-natural-language', 'compression', 'document-uri'], true)) {
                    continue;
                }
                $docAttrs->set($name, $value);
            }
            if (empty($docAttrs->jsonSerialize())) {
                $docAttrs = null;
            }
        }

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::CREATE_DOCUMENT,
            $operationAttributes
        );

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('2.0'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::CREATE_DOCUMENT),
            new \obray\ipp\types\Integer($requestId),
            null,
            $operationAttributes
        );

        if ($docAttrs !== null) {
            $payload->documentAttributes = $docAttrs;
        }

        return $this->sendPayload($payload);
    }

    /**
     * Set Job Attributes
     *
     * RFC 8011 4.2.20:
     * This OPTIONAL operation allows a client to modify the values of one or
     * more Job object attributes. The job MUST be in the pending or held
     * state for this operation to succeed.
     *
     * @param array $attributes Associative array of job attribute names to set
     * @param int   $requestId  Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function setJobAttributes(array $attributes, int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        $jobAttributes = new \obray\ipp\JobAttributes($attributes);

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::SET_JOB_ATTRIBUTES,
            $operationAttributes
        );

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('2.0'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::SET_JOB_ATTRIBUTES),
            new \obray\ipp\types\Integer($requestId),
            null,
            $operationAttributes,
            $jobAttributes
        );

        return $this->sendPayload($payload);
    }

}
