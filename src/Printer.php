<?php

namespace obray\ipp;

class Printer
{
    private readonly string $printerURI;
    private readonly string $user;
    private readonly string $password;
    private readonly array $curlOptions;
    private string $requestClass;

    public function __construct(string $uri, string $user = '', string $password = '', array $curlOptions = [], ?string $requestClass = null)
    {
        $this->printerURI = $uri;
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
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        if ($this->user !== '') {
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
            'document-uri',
            'compression',
            'ipp-attribute-fidelity',
            'job-name',
            'last-document',
            'limit',
            'my-jobs',
            'my-subscriptions',
            'notify-job-id',
            'requested-attributes',
            'resource-format',
            'resource-name',
            'resource-type',
            'which-jobs',
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

    private function createJobAttributes(?array $attributes = null): ?\obray\ipp\JobAttributes
    {
        if (!is_array($attributes) || $attributes === []) {
            return null;
        }

        return new \obray\ipp\JobAttributes($attributes);
    }

    private function sendPayload(\obray\ipp\transport\IPPPayload $payload): \obray\ipp\transport\IPPPayload
    {
        $requestClass = $this->requestClass;

        return $requestClass::send(
            $this->printerURI,
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
        ?\obray\ipp\JobAttributes $jobAttributes = null,
        ?\obray\ipp\types\OctetString $document = null,
        string $versionNumber = '1.1'
    ): \obray\ipp\transport\IPPPayload {
        \obray\ipp\spec\OperationRequestValidator::validate(
            $operationCode,
            $operationAttributes,
            $jobAttributes,
            $document
        );

        return new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber($versionNumber),
            new \obray\ipp\types\Operation($operationCode),
            new \obray\ipp\types\Integer($requestId),
            $document,
            $operationAttributes,
            $jobAttributes
        );
    }

    private function getFirstAttributeGroup($attributeGroups): ?\obray\ipp\AttributeGroup
    {
        if ($attributeGroups instanceof \obray\ipp\AttributeGroup) {
            return $attributeGroups;
        }

        if (!is_array($attributeGroups) || $attributeGroups === []) {
            return null;
        }

        $first = reset($attributeGroups);

        return $first instanceof \obray\ipp\AttributeGroup ? $first : null;
    }

    private function extractAttributeValues($attribute): array
    {
        if ($attribute === null) {
            return [];
        }

        if (!is_array($attribute)) {
            return [(string) $attribute];
        }

        return array_map(static fn ($value) => (string) $value, $attribute);
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

    public function printJob(string $document, int $requestId=1, ?array $attributes=null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes($attributes);
        $jobAttributes = $this->createJobAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::PRINT_JOB,
                $requestId,
                $operationAttributes,
                $jobAttributes,
                new \obray\ipp\types\OctetString($document)
            )
        );
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

    public function printURI(string $documentURI, int $requestId=1, ?array $attributes=null): \obray\ipp\transport\IPPPayload
    {
        $attributes = $attributes ?? [];
        $attributes['document-uri'] = $documentURI;

        $operationAttributes = $this->createOperationAttributes($attributes);
        $jobAttributes = $this->createJobAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::PRINT_URI,
                $requestId,
                $operationAttributes,
                $jobAttributes
            )
        );
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

    public function validateJob(int $requestId=1, ?array $attributes=null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes($attributes);
        $jobAttributes = $this->createJobAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::VALIDATE_JOB,
                $requestId,
                $operationAttributes,
                $jobAttributes
            )
        );
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

    public function createJob(int $requestId=1, ?array $attributes=null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes($attributes);
        $jobAttributes = $this->createJobAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CREATE_JOB,
                $requestId,
                $operationAttributes,
                $jobAttributes
            )
        );
    }

    public function getSupportedMedia(): array
    {
        $response = $this->getPrinterAttributes(1, ['media-supported']);
        $printerAttributes = $this->getFirstAttributeGroup($response->printerAttributes);

        if ($printerAttributes === null || !$printerAttributes->has('media-supported')) {
            return [];
        }

        return $this->extractAttributeValues($printerAttributes->{'media-supported'});
    }

    public function getSupportedResolutions(): array
    {
        $response = $this->getPrinterAttributes(1, ['printer-resolution-supported']);
        $printerAttributes = $this->getFirstAttributeGroup($response->printerAttributes);

        if ($printerAttributes === null || !$printerAttributes->has('printer-resolution-supported')) {
            return [];
        }

        return $this->extractAttributeValues($printerAttributes->{'printer-resolution-supported'});
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

    public function getPrinterAttributes(int $requestId=1, ?array $requestedAttributes = null, ?string $documentFormat = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }
        if ($documentFormat !== null) {
            $attributes['document-format'] = $documentFormat;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_PRINTER_ATTRIBUTES,
                $requestId,
                $operationAttributes,
                null,
                null
            )
        );
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

    public function getJobs(int $requestId = 1, ?string $whichJobs = null, ?int $limit = null, ?bool $myJobs = null, ?array $requestedAttributes = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($whichJobs !== null) {
            $attributes['which-jobs'] = $whichJobs;
        }
        if ($limit !== null) {
            $attributes['limit'] = $limit;
        }
        if ($myJobs !== null) {
            $attributes['my-jobs'] = $myJobs;
        }
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_JOBS,
                $requestId,
                $operationAttributes,
                null,
                null
            )
        );
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

    public function pausePrinter(int $requestId=1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::PAUSE_PRINTER,
                $requestId,
                $operationAttributes
            )
        );
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

    public function resumePrinter(int $requestId=1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::RESUME_PRINTER,
                $requestId,
                $operationAttributes
            )
        );
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

    public function purgeJobs(int $requestId=1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::PURGE_JOBS,
                $requestId,
                $operationAttributes
            )
        );
    }

    /**
     * CUPS Get Default
     *
     * Returns the attributes of the default destination printer on a CUPS
     * server. The Printer object should be initialized with the CUPS server
     * URI (e.g. ipp://localhost/).
     *
     * @param int        $requestId           Client request id
     * @param array|null $requestedAttributes Optional list of attribute names to return
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function getDefault(int $requestId=1, ?array $requestedAttributes=null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CUPS_GET_DEFAULT,
                $requestId,
                $operationAttributes
            )
        );
    }

    /**
     * CUPS Get Printers
     *
     * Returns a list of printers known to the CUPS server. The Printer
     * object should be initialized with the CUPS server URI (e.g.
     * ipp://localhost/).
     *
     * @param int        $requestId           Client request id
     * @param array|null $requestedAttributes Optional list of attribute names to return
     * @param int|null   $limit               Maximum number of printers to return
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function getPrinters(int $requestId=1, ?array $requestedAttributes=null, ?int $limit=null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }
        if ($limit !== null) {
            $attributes['limit'] = $limit;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CUPS_GET_PRINTERS,
                $requestId,
                $operationAttributes
            )
        );
    }

    /**
     * CUPS Get Classes
     *
     * Returns a list of printer classes known to the CUPS server. The
     * Printer object should be initialized with the CUPS server URI (e.g.
     * ipp://localhost/).
     *
     * @param int        $requestId           Client request id
     * @param array|null $requestedAttributes Optional list of attribute names to return
     * @param int|null   $limit               Maximum number of classes to return
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function getClasses(int $requestId=1, ?array $requestedAttributes=null, ?int $limit=null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }
        if ($limit !== null) {
            $attributes['limit'] = $limit;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CUPS_GET_CLASSES,
                $requestId,
                $operationAttributes
            )
        );
    }

    public function getPrinterSupportedValues(int $requestId = 1, ?array $attributes = null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_PRINTER_SUPPORTED_VALUES,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function getResourceAttributes(int $requestId = 1, ?array $attributes = null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_RESOURCE_ATTRIBUTES,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function getResourceData(int $requestId = 1, ?array $attributes = null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_RESOURCE_DATA,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function getResources(int $requestId = 1, ?array $attributes = null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_RESOURCES,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function enablePrinter(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::ENABLE_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function disablePrinter(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::DISABLE_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function pausePrinterAfterCurrentJob(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::PAUSE_PRINTER_AFTER_CURRENT_JOB,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function holdNewJobs(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::HOLD_NEW_JOBS,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function releaseHeldNewJobs(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::RELEASE_HELD_NEW_JOBS,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function deactivatePrinter(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::DEACTIVATE_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function activatePrinter(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::ACTIVATE_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function restartPrinter(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::RESTART_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function shutdownPrinter(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::SHUTDOWN_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function startPrinter(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::START_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function cancelJobs(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CANCEL_JOBS,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    public function cancelMyJobs(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CANCEL_MY_JOBS,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    /**
     * Set Printer Attributes
     *
     * RFC 8011 4.2.19:
     * This OPTIONAL operation allows a client to modify the values of one or
     * more Printer object attributes. Only a privileged user (e.g. operator
     * or administrator) should be permitted to perform this operation.
     *
     * @param array $attributes Associative array of printer attribute names to set
     * @param int   $requestId  Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    /**
     * Identify Printer
     *
     * RFC 8011 §4.2.22:
     * This OPTIONAL operation causes the Printer to perform one or more
     * human-perceptible actions (e.g. flash a light, sound a tone, display
     * a message) so that a user can identify which physical device corresponds
     * to the Printer object.
     *
     * @param int        $requestId       Client request id
     * @param array|null $identifyActions Actions to perform (e.g. ['flash', 'sound'])
     * @param string|null $message        Optional message to display on the printer
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function identifyPrinter(int $requestId = 1, ?array $identifyActions = null, ?string $message = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($identifyActions !== null) {
            $attributes['identify-actions'] = $identifyActions;
        }
        if ($message !== null) {
            $attributes['message'] = $message;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::IDENTIFY_PRINTER,
                $requestId,
                $operationAttributes,
                null,
                null,
                '2.0'
            )
        );
    }

    /**
     * Create Printer Subscription
     *
     * RFC 3995 §11.1.1:
     * This OPTIONAL operation creates a subscription associated with a Printer
     * object. The client supplies one Subscription Template Attributes group.
     *
     * @param array $subscriptionAttributes Associative array of subscription attributes
     * @param int   $requestId              Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function createPrinterSubscription(array $subscriptionAttributes, int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        $subAttrs = new \obray\ipp\SubscriptionAttributes();
        foreach ($subscriptionAttributes as $name => $value) {
            $subAttrs->set($name, $value);
        }

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::CREATE_PRINTER_SUBSCRIPTION,
            $operationAttributes
        );

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::CREATE_PRINTER_SUBSCRIPTION),
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
     * Get Subscriptions
     *
     * RFC 3995 §11.2.2:
     * This OPTIONAL operation returns the attributes of one or more existing
     * Subscription objects associated with the Printer. The client may filter
     * by job, user, or subscription ID.
     *
     * @param int        $requestId           Client request id
     * @param int|null   $notifyJobId         Limit results to subscriptions for this job
     * @param array|null $requestedAttributes List of attribute names to return
     * @param bool|null  $mySubscriptions     If true, return only the caller's subscriptions
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function getSubscriptions(int $requestId = 1, ?int $notifyJobId = null, ?array $requestedAttributes = null, ?bool $mySubscriptions = null): \obray\ipp\transport\IPPPayload
    {
        $attributes = [];
        if ($notifyJobId !== null) {
            $attributes['notify-job-id'] = $notifyJobId;
        }
        if ($requestedAttributes !== null) {
            $attributes['requested-attributes'] = $requestedAttributes;
        }
        if ($mySubscriptions !== null) {
            $attributes['my-subscriptions'] = $mySubscriptions;
        }

        $operationAttributes = $this->createOperationAttributes($attributes);

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_SUBSCRIPTION,
                $requestId,
                $operationAttributes
            )
        );
    }

    /**
     * Set Printer Attributes
     *
     * RFC 8011 §4.2.19:
     * This OPTIONAL operation allows a client to modify the values of one or
     * more Printer object attributes. Only a privileged user (e.g. operator
     * or administrator) should be permitted to perform this operation.
     *
     * @param array $attributes Associative array of printer attribute names to set
     * @param int   $requestId  Client request id
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function setPrinterAttributes(array $attributes, int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        $printerAttributes = new \obray\ipp\PrinterAttributes();
        foreach ($attributes as $name => $value) {
            $printerAttributes->set($name, $value);
        }

        \obray\ipp\spec\OperationRequestValidator::validate(
            \obray\ipp\types\Operation::SET_PRINTER_ATTRIBUTES,
            $operationAttributes
        );

        $payload = new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('2.0'),
            new \obray\ipp\types\Operation(\obray\ipp\types\Operation::SET_PRINTER_ATTRIBUTES),
            new \obray\ipp\types\Integer($requestId),
            null,
            $operationAttributes,
            null,
            $printerAttributes
        );

        return $this->sendPayload($payload);
    }
}
