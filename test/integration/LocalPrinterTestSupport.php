<?php
declare(strict_types=1);

final class LocalPrinterTarget
{
    private ?array $operationsSupported = null;

    public function __construct(
        public readonly string $uri,
        public readonly ?string $queue,
        public readonly string $user,
        public readonly string $password
    ) {
    }

    public static function discover(bool $requireJobManagement = false): ?self
    {
        $configuredUri = trim((string) getenv('IPP_TEST_URI'));
        $configuredQueue = trim((string) getenv('IPP_TEST_QUEUE'));
        $user = self::currentUser();
        $password = (string) getenv('IPP_TEST_PASSWORD');

        if ($configuredUri !== '') {
            $target = new self($configuredUri, self::queueFromUri($configuredUri) ?? ($configuredQueue !== '' ? $configuredQueue : null), $user, $password);

            return self::supportsRequiredOperations($target, $requireJobManagement) ? $target : null;
        }

        if ($configuredQueue !== '') {
            $target = new self(
                sprintf('ipp://localhost/printers/%s', rawurlencode($configuredQueue)),
                $configuredQueue,
                $user,
                $password
            );

            return self::supportsRequiredOperations($target, $requireJobManagement) ? $target : null;
        }

        foreach (self::cupsQueues() as $queue) {
            $target = new self(
                sprintf('ipp://localhost/printers/%s', rawurlencode($queue)),
                $queue,
                $user,
                $password
            );

            if (self::supportsRequiredOperations($target, $requireJobManagement)) {
                return $target;
            }
        }

        foreach (self::networkUris() as $uri) {
            $target = new self($uri, self::queueFromUri($uri), $user, $password);

            if (self::supportsRequiredOperations($target, $requireJobManagement)) {
                return $target;
            }
        }

        return null;
    }

    public function printer(): \obray\ipp\Printer
    {
        return new \obray\ipp\Printer($this->uri, $this->user, $this->password);
    }

    public function job(int|string $jobId): \obray\ipp\Job
    {
        return new \obray\ipp\Job($this->uri, $jobId, $this->user, $this->password);
    }

    public function subscription(int $subscriptionId): \obray\ipp\Subscription
    {
        return new \obray\ipp\Subscription($this->uri, $subscriptionId, $this->user, $this->password);
    }

    public function supportsSubscriptions(): bool
    {
        return $this->supportsOperations([
            'create-printer-subscription',
            'get-subscription',
            'cancel-subscription',
        ]);
    }

    public function supportsDocumentObjects(): bool
    {
        return $this->supportsOperations([
            'get-documents',
            'get-document-attributes',
        ]);
    }

    public static function firstAttributeGroup(?array $attributeGroups): ?\obray\ipp\AttributeGroup
    {
        if (!is_array($attributeGroups) || $attributeGroups === []) {
            return null;
        }

        $first = reset($attributeGroups);

        return $first instanceof \obray\ipp\AttributeGroup ? $first : null;
    }

    public static function attributeValues($attribute): array
    {
        if ($attribute === null) {
            return [];
        }

        if (!is_array($attribute)) {
            return [(string) $attribute];
        }

        return array_map(static fn ($value) => (string) $value, $attribute);
    }

    private static function supportsRequiredOperations(self $target, bool $requireJobManagement): bool
    {
        $required = $requireJobManagement
            ? ['create-job', 'send-document', 'cancel-job', 'get-job-attributes']
            : ['get-printer-attributes', 'validate-job', 'get-jobs'];

        return $target->supportsOperations($required);
    }

    private function supportsOperations(array $requiredOperations): bool
    {
        $operations = $this->operationsSupported();
        if ($operations === []) {
            return false;
        }

        foreach ($requiredOperations as $operation) {
            if (!in_array($operation, $operations, true)) {
                return false;
            }
        }

        return true;
    }

    private function operationsSupported(): array
    {
        if ($this->operationsSupported !== null) {
            return $this->operationsSupported;
        }

        try {
            $response = $this->printer()->getPrinterAttributes(1, ['operations-supported']);
        } catch (\Throwable) {
            return $this->operationsSupported = [];
        }

        $printerAttributes = self::firstAttributeGroup($response->printerAttributes);
        if ($printerAttributes === null || !$printerAttributes->has('operations-supported')) {
            return $this->operationsSupported = [];
        }

        return $this->operationsSupported = self::attributeValues($printerAttributes->{'operations-supported'});
    }

    private static function cupsQueues(): array
    {
        $output = shell_exec('lpstat -e 2>/dev/null');
        if (!is_string($output) || trim($output) === '') {
            return [];
        }

        $queues = preg_split('/\R+/', trim($output)) ?: [];
        $queues = array_values(array_filter($queues, static fn ($queue) => $queue !== ''));

        $default = trim((string) shell_exec('lpstat -d 2>/dev/null | sed -n "s/^system default destination: //p"'));
        if ($default !== '' && in_array($default, $queues, true)) {
            $queues = array_values(array_unique(array_merge([$default], $queues)));
        }

        return $queues;
    }

    private static function currentUser(): string
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

    private static function networkUris(): array
    {
        $uris = [];

        foreach (['ippfind -T 5 _ipp._tcp --print', 'ippfind -T 5 _ipps._tcp --print'] as $command) {
            $output = shell_exec($command . ' 2>/dev/null');
            if (!is_string($output) || trim($output) === '') {
                continue;
            }

            foreach (preg_split('/\R+/', trim($output)) ?: [] as $uri) {
                if ($uri === '') {
                    continue;
                }

                $uris[] = $uri;
            }
        }

        return array_values(array_unique($uris));
    }

    private static function queueFromUri(string $uri): ?string
    {
        $parts = parse_url($uri);
        if (!is_array($parts) || empty($parts['path'])) {
            return null;
        }

        if (!preg_match('#/printers/([^/]+)$#', $parts['path'], $matches)) {
            return null;
        }

        return rawurldecode($matches[1]);
    }
}
