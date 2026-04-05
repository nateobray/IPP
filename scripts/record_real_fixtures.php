<?php
declare(strict_types=1);

use obray\ipp\Printer;
use obray\ipp\test\support\FixtureRecordingRequest;
use obray\ipp\test\support\RealFixtureSummary;

require dirname(__DIR__) . '/vendor/autoload.php';

$fixtureRoot = dirname(__DIR__) . '/test/fixtures/real';
$user = currentUser();
$password = (string) getenv('IPP_TEST_PASSWORD');
$curlOptions = insecureCurlOptionsIfRequested();
$preferredFormatsByTarget = [];

$targets = discoverTargets($user, $password);
if ($targets === []) {
    fwrite(STDERR, "No reachable printer targets discovered.\n");
    exit(1);
}

foreach ($targets as $target) {
    $targetDir = $fixtureRoot . '/' . $target['source'] . '/' . sanitizePathSegment($target['label']);
    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
        fwrite(STDERR, sprintf("Failed to create fixture directory %s\n", $targetDir));
        exit(1);
    }

    $documentFormat = null;

    echo sprintf("[%s] %s\n", $target['source'], $target['uri']);

    $getPrinterAttributes = recordOperation(
        $target,
        $targetDir,
        'get-printer-attributes',
        ['requested_attributes' => requestedPrinterAttributes()],
        static fn (Printer $printer) => $printer->getPrinterAttributes(
            2001,
            requestedPrinterAttributes()
        )
    );

    if ($getPrinterAttributes instanceof \obray\ipp\transport\IPPPayload) {
        $documentFormat = preferredDocumentFormat($getPrinterAttributes);
        $preferredFormatsByTarget[$target['source'] . '|' . $target['uri']] = $documentFormat;
    }

    recordOperation(
        $target,
        $targetDir,
        'validate-job',
        ['document_format' => $documentFormat ?? 'application/octet-stream', 'job_hold_until' => 'indefinite'],
        static fn (Printer $printer) => $printer->validateJob(2002, [
            'document-format' => $documentFormat ?? 'application/octet-stream',
            'job-hold-until' => 'indefinite',
        ])
    );

    recordOperation(
        $target,
        $targetDir,
        'get-jobs',
        ['which_jobs' => 'not-completed', 'limit' => 5, 'requested_attributes' => ['job-id', 'job-name', 'job-state']],
        static fn (Printer $printer) => $printer->getJobs(2003, 'not-completed', 5, false, ['job-id', 'job-name', 'job-state'])
    );

    recordOperation(
        $target,
        $targetDir,
        'validate-job-unsupported-format',
        ['document_format' => 'application/x-obray-ipp-compliance-probe'],
        static fn (Printer $printer) => $printer->validateJob(2004, [
            'document-format' => 'application/x-obray-ipp-compliance-probe',
        ])
    );
}

recordHeldPrintJobLifecycle($targets, $fixtureRoot, $preferredFormatsByTarget);
recordCancelJobAuthenticationChallenge($targets, $fixtureRoot, $curlOptions);

echo "Recorded real response fixtures.\n";

function recordOperation(array $target, string $targetDir, string $operation, array $requestMeta, callable $callback): ?\obray\ipp\transport\IPPPayload
{
    FixtureRecordingRequest::reset();

    try {
        /** @var \obray\ipp\transport\IPPPayload $payload */
        $payload = $callback(new Printer($target['uri'], $target['user'], $target['password'], $target['curl_options'], FixtureRecordingRequest::class));
    } catch (\Throwable $exception) {
        fwrite(STDERR, sprintf("  - %s failed: %s\n", $operation, $exception->getMessage()));
        return null;
    }

    $exchange = FixtureRecordingRequest::$lastExchange;
    if ($exchange === []) {
        fwrite(STDERR, sprintf("  - %s produced no exchange data\n", $operation));
        return null;
    }

    $requestPath = $targetDir . '/' . $operation . '.request.bin';
    $responsePath = $targetDir . '/' . $operation . '.response.bin';
    $metaPath = $targetDir . '/' . $operation . '.meta.json';

    $meta = [
        'recorded_at' => date(DATE_ATOM),
        'kind' => 'ipp-response',
        'source' => $target['source'],
        'label' => $target['label'],
        'printer_uri' => $target['uri'],
        'operation' => $operation,
        'request' => $requestMeta,
        'request_file' => basename($requestPath),
        'response_file' => basename($responsePath),
        'http' => [
            'post_url' => $exchange['post_url'],
            'status' => $exchange['http_info']['http_code'] ?? null,
        ],
        'summary' => RealFixtureSummary::fromPayload($payload),
    ];

    writeFixtureFiles($requestPath, $responsePath, $metaPath, $exchange['encoded_request'], $exchange['raw_response'], $meta);

    echo sprintf("  - %s => %s\n", $operation, $meta['summary']['status']);

    return $payload;
}

function recordHeldPrintJobLifecycle(array $targets, string $fixtureRoot, array $preferredFormatsByTarget): void
{
    $cupsTargets = array_values(array_filter($targets, static fn (array $target) => $target['source'] === 'cups'));
    if ($cupsTargets === []) {
        return;
    }

    foreach ($cupsTargets as $target) {
        $targetKey = $target['source'] . '|' . $target['uri'];
        $documentFormat = $preferredFormatsByTarget[$targetKey] ?? 'text/plain';
        $targetDir = $fixtureRoot . '/' . $target['source'] . '/' . sanitizePathSegment($target['label']);
        $printer = new Printer($target['uri'], $target['user'], $target['password'], $target['curl_options'], FixtureRecordingRequest::class);
        $jobId = null;
        $jobUri = null;

        try {
            FixtureRecordingRequest::reset();
            $printResponse = $printer->printJob("recorded lifecycle probe\n", 2020, [
                'document-format' => $documentFormat,
                'job-name' => 'IPP Recorded Lifecycle Probe',
                'job-hold-until' => 'indefinite',
            ]);
            $printExchange = FixtureRecordingRequest::$lastExchange;

            $jobAttributes = is_array($printResponse->jobAttributes) ? ($printResponse->jobAttributes[0] ?? null) : null;
            if (!$jobAttributes instanceof \obray\ipp\JobAttributes || !$jobAttributes->has('job-id')) {
                continue;
            }

            $jobId = (int) $jobAttributes->{'job-id'}->getAttributeValue();
            if ($jobAttributes->has('job-uri')) {
                $jobUri = (string) $jobAttributes->{'job-uri'};
            }

            writeRecordedPayloadFixture(
                $target,
                $targetDir,
                'print-job-held',
                [
                    'document_format' => $documentFormat,
                    'job_name' => 'IPP Recorded Lifecycle Probe',
                    'job_hold_until' => 'indefinite',
                ],
                $printExchange,
                $printResponse
            );

            $job = new \obray\ipp\Job($target['uri'], $jobId, $target['user'], $target['password'], $target['curl_options'], FixtureRecordingRequest::class);

            FixtureRecordingRequest::reset();
            $getJobResponse = $job->getJobAttributes(2021, requestedJobDescriptionAttributes());
            writeRecordedPayloadFixture(
                $target,
                $targetDir,
                'get-job-attributes',
                [
                    'job_id' => $jobId,
                    'requested_attributes' => requestedJobDescriptionAttributes(),
                ],
                FixtureRecordingRequest::$lastExchange,
                $getJobResponse
            );

            if ($jobUri !== null && $jobUri !== '') {
                $jobByUri = new \obray\ipp\Job($target['uri'], $jobUri, $target['user'], $target['password'], $target['curl_options'], FixtureRecordingRequest::class);

                FixtureRecordingRequest::reset();
                $getJobByUriResponse = $jobByUri->getJobAttributes(2023, requestedJobDescriptionAttributes());
                writeRecordedPayloadFixture(
                    $target,
                    $targetDir,
                    'get-job-attributes-by-job-uri',
                    [
                        'job_id' => $jobId,
                        'job_uri' => $jobUri,
                        'requested_attributes' => requestedJobDescriptionAttributes(),
                    ],
                    FixtureRecordingRequest::$lastExchange,
                    $getJobByUriResponse
                );
            }

            FixtureRecordingRequest::reset();
            $cancelResponse = $job->cancelJob(2022);
            writeRecordedPayloadFixture(
                $target,
                $targetDir,
                'cancel-job-authenticated',
                [
                    'job_id' => $jobId,
                    'with_user' => true,
                ],
                FixtureRecordingRequest::$lastExchange,
                $cancelResponse
            );

            echo "  - print-job-held => " . (string) $printResponse->statusCode . "\n";
            echo "  - get-job-attributes => " . (string) $getJobResponse->statusCode . "\n";
            if (isset($getJobByUriResponse)) {
                echo "  - get-job-attributes-by-job-uri => " . (string) $getJobByUriResponse->statusCode . "\n";
            }
            echo "  - cancel-job-authenticated => " . (string) $cancelResponse->statusCode . "\n";
            return;
        } catch (\Throwable $exception) {
            if ($jobId !== null) {
                cleanupRecordedJob($target, $jobId);
            }
            continue;
        }
    }
}

function recordCancelJobAuthenticationChallenge(array $targets, string $fixtureRoot, array $curlOptions): void
{
    $cupsTargets = array_values(array_filter($targets, static fn (array $target) => $target['source'] === 'cups'));
    if ($cupsTargets === []) {
        return;
    }

    foreach ($cupsTargets as $target) {
        $targetDir = $fixtureRoot . '/' . $target['source'] . '/' . sanitizePathSegment($target['label']);
        $printer = new Printer($target['uri'], $target['user'], $target['password'], $curlOptions, FixtureRecordingRequest::class);

        try {
            $printResponse = $printer->printJob("auth challenge probe\n", 2010, [
                'document-format' => 'text/plain',
                'job-name' => 'IPP Auth Challenge Probe',
                'job-hold-until' => 'indefinite',
            ]);
        } catch (\Throwable $exception) {
            continue;
        }

        $jobAttributes = is_array($printResponse->jobAttributes) ? ($printResponse->jobAttributes[0] ?? null) : null;
        if (!$jobAttributes instanceof \obray\ipp\JobAttributes || !$jobAttributes->has('job-id')) {
            continue;
        }

        $jobId = (int) $jobAttributes->{'job-id'}->getAttributeValue();
        $requestBinary = buildCancelJobRequest($target['uri'], $jobId, 2011);

        try {
            $rawResponse = \obray\ipp\Request::sendRaw($target['uri'], $requestBinary, null, null, $curlOptions);
        } catch (\Throwable $exception) {
            try {
                (new \obray\ipp\Job($target['uri'], $jobId, $target['user'], $target['password'], $curlOptions))->cancelJob(2012);
            } catch (\Throwable) {
            }
            continue;
        }

        try {
            (new \obray\ipp\Job($target['uri'], $jobId, $target['user'], $target['password'], $target['curl_options']))->cancelJob(2013);
        } catch (\Throwable) {
        }

        $httpStatus = $rawResponse['http_info']['http_code'] ?? 0;
        if ($httpStatus !== 401) {
            continue;
        }

        $requestPath = $targetDir . '/cancel-job-unauthenticated.request.bin';
        $responsePath = $targetDir . '/cancel-job-unauthenticated.response.bin';
        $metaPath = $targetDir . '/cancel-job-unauthenticated.meta.json';

        $meta = [
            'recorded_at' => date(DATE_ATOM),
            'kind' => 'http-error',
            'source' => $target['source'],
            'label' => $target['label'],
            'printer_uri' => $target['uri'],
            'operation' => 'cancel-job-unauthenticated',
            'request' => [
                'job_id' => $jobId,
                'with_user' => false,
            ],
            'request_file' => basename($requestPath),
            'response_file' => basename($responsePath),
            'http' => [
                'post_url' => $rawResponse['post_url'],
                'status' => $httpStatus,
            ],
            'error' => [
                'class' => \obray\ipp\exceptions\AuthenticationError::class,
                'message' => 'Authentication error: make sure you are passing the correct credentials.',
            ],
        ];

        writeFixtureFiles($requestPath, $responsePath, $metaPath, $requestBinary, (string) $rawResponse['body'], $meta);
        echo "  - cancel-job-unauthenticated => http-401\n";
        return;
    }
}

function discoverTargets(string $user, string $password): array
{
    $targets = [];

    $configuredUri = trim((string) getenv('IPP_RECORD_URI'));
    if ($configuredUri !== '') {
        return [[
            'source' => 'manual',
            'label' => parse_url($configuredUri, PHP_URL_HOST) ?: 'target',
            'uri' => $configuredUri,
            'user' => $user,
            'password' => $password,
            'curl_options' => insecureCurlOptionsIfRequested(),
        ]];
    }

    foreach (cupsQueues() as $queue) {
        $targets[] = [
            'source' => 'cups',
            'label' => $queue,
            'uri' => 'ipp://localhost/printers/' . rawurlencode($queue),
            'user' => $user,
            'password' => $password,
            'curl_options' => insecureCurlOptionsIfRequested(),
        ];
    }

    foreach (networkUris() as $uri) {
        $host = (string) parse_url($uri, PHP_URL_HOST);
        $targets[] = [
            'source' => 'network',
            'label' => $host !== '' ? $host : $uri,
            'uri' => $uri,
            'user' => $user,
            'password' => $password,
            'curl_options' => insecureCurlOptionsIfRequested(),
        ];
    }

    $deduped = [];
    foreach ($targets as $target) {
        $deduped[$target['source'] . '|' . $target['uri']] = $target;
    }

    return array_values($deduped);
}

function cupsQueues(): array
{
    $output = shell_exec('lpstat -e 2>/dev/null');
    if (!is_string($output) || trim($output) === '') {
        return [];
    }

    return array_values(array_filter(preg_split('/\R+/', trim($output)) ?: []));
}

function networkUris(): array
{
    $uris = [];
    $commands = ['ippfind -T 5 _ipp._tcp --print'];

    if (filter_var(getenv('IPP_RECORD_INCLUDE_IPPS'), FILTER_VALIDATE_BOOL)) {
        $commands[] = 'ippfind -T 5 _ipps._tcp --print';
    }

    foreach ($commands as $command) {
        $output = shell_exec($command . ' 2>/dev/null');
        if (!is_string($output) || trim($output) === '') {
            continue;
        }

        foreach (preg_split('/\R+/', trim($output)) ?: [] as $uri) {
            if ($uri !== '') {
                $uris[] = $uri;
            }
        }
    }

    return array_values(array_unique($uris));
}

function preferredDocumentFormat(\obray\ipp\transport\IPPPayload $payload): string
{
    $preferred = [
        'text/plain',
        'application/octet-stream',
        'application/pdf',
        'image/pwg-raster',
        'image/urf',
    ];

    $printerAttributes = $payload->printerAttributes;
    if ($printerAttributes instanceof \obray\ipp\AttributeGroup) {
        $printerAttributes = [$printerAttributes];
    }

    if (!is_array($printerAttributes) || $printerAttributes === []) {
        return 'application/octet-stream';
    }

    $firstGroup = reset($printerAttributes);
    if (!$firstGroup instanceof \obray\ipp\AttributeGroup || !$firstGroup->has('document-format-supported')) {
        return 'application/octet-stream';
    }

    $formats = $firstGroup->{'document-format-supported'};
    $supported = [];
    if (is_array($formats)) {
        foreach ($formats as $format) {
            $supported[] = (string) $format;
        }
    } else {
        $supported[] = (string) $formats;
    }

    foreach ($preferred as $candidate) {
        if (in_array($candidate, $supported, true)) {
            return $candidate;
        }
    }

    return $supported[0] ?? 'application/octet-stream';
}

function currentUser(): string
{
    $configuredUser = trim((string) getenv('IPP_TEST_USER'));
    if ($configuredUser !== '') {
        return $configuredUser;
    }

    $shellUser = trim((string) getenv('USER'));
    if ($shellUser !== '') {
        return $shellUser;
    }

    return trim((string) shell_exec('whoami 2>/dev/null'));
}

function requestedPrinterAttributes(): array
{
    return \obray\ipp\spec\Rfc2911AttributeMatrix::requiredPrinterDescriptionAttributeNames();
}

function requestedJobDescriptionAttributes(): array
{
    return \obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributeNames();
}

function sanitizePathSegment(string $segment): string
{
    $segment = preg_replace('/[^A-Za-z0-9._-]+/', '_', $segment) ?? 'target';
    return trim($segment, '_');
}

function buildCancelJobRequest(string $printerUri, int $jobId, int $requestId): string
{
    $operationAttributes = new \obray\ipp\OperationAttributes();
    $operationAttributes->{'printer-uri'} = $printerUri;
    $operationAttributes->{'job-id'} = $jobId;

    $payload = new \obray\ipp\transport\IPPPayload(
        new \obray\ipp\types\VersionNumber('1.1'),
        new \obray\ipp\types\Operation(\obray\ipp\types\Operation::CANCEL_JOB),
        new \obray\ipp\types\Integer($requestId),
        null,
        $operationAttributes
    );

    return $payload->encode();
}

function writeRecordedPayloadFixture(
    array $target,
    string $targetDir,
    string $operation,
    array $requestMeta,
    array $exchange,
    \obray\ipp\transport\IPPPayload $payload
): void {
    $requestPath = $targetDir . '/' . $operation . '.request.bin';
    $responsePath = $targetDir . '/' . $operation . '.response.bin';
    $metaPath = $targetDir . '/' . $operation . '.meta.json';

    $meta = [
        'recorded_at' => date(DATE_ATOM),
        'kind' => 'ipp-response',
        'source' => $target['source'],
        'label' => $target['label'],
        'printer_uri' => $target['uri'],
        'operation' => $operation,
        'request' => $requestMeta,
        'request_file' => basename($requestPath),
        'response_file' => basename($responsePath),
        'http' => [
            'post_url' => $exchange['post_url'],
            'status' => $exchange['http_info']['http_code'] ?? null,
        ],
        'summary' => RealFixtureSummary::fromPayload($payload),
    ];

    writeFixtureFiles($requestPath, $responsePath, $metaPath, $exchange['encoded_request'], $exchange['raw_response'], $meta);
}

function writeFixtureFiles(
    string $requestPath,
    string $responsePath,
    string $metaPath,
    string $requestBinary,
    string $responseBinary,
    array $meta
): void {
    file_put_contents($requestPath, $requestBinary);
    file_put_contents($responsePath, $responseBinary);
    file_put_contents($metaPath, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
}

function cleanupRecordedJob(array $target, int $jobId): void
{
    try {
        (new \obray\ipp\Job($target['uri'], $jobId, $target['user'], $target['password'], $target['curl_options']))->cancelJob(2099);
        return;
    } catch (\Throwable) {
    }

    if ($target['source'] !== 'cups') {
        return;
    }

    $jobName = $target['label'] . '-' . $jobId;
    shell_exec('cancel ' . escapeshellarg($jobName) . ' 2>/dev/null');
}

function insecureCurlOptionsIfRequested(): array
{
    if (!filter_var(getenv('IPP_RECORD_INSECURE_TLS'), FILTER_VALIDATE_BOOL)) {
        return [];
    }

    return [
        ['key' => CURLOPT_SSL_VERIFYPEER, 'value' => false],
        ['key' => CURLOPT_SSL_VERIFYHOST, 'value' => false],
    ];
}
