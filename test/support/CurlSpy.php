<?php
declare(strict_types=1);

namespace obray\ipp\test {
    // Loaded only inside isolated RequestTransportTest processes.
    final class CurlSpy
    {
        public static array $options = [];
        public static array $calls = [];
        public static string $body = '';
        public static int $errorCode = 0;
        public static int $httpCode = 200;
        public static bool $closed = false;
    }
}

namespace obray\ipp {
    use obray\ipp\test\CurlSpy;

    function curl_init(): \stdClass
    {
        return new \stdClass();
    }

    function curl_setopt($handle, int $option, $value): bool
    {
        CurlSpy::$options[$option] = $value;
        CurlSpy::$calls[] = [$option, $value];
        return true;
    }

    function curl_exec($handle): string|false
    {
        return CurlSpy::$errorCode === 0 ? CurlSpy::$body : false;
    }

    function curl_errno($handle): int
    {
        if (CurlSpy::$closed) {
            throw new \LogicException('Error details must be captured before closing the handle.');
        }
        return CurlSpy::$errorCode;
    }

    function curl_error($handle): string
    {
        return 'Operation timed out';
    }

    function curl_getinfo($handle): array
    {
        return ['http_code' => CurlSpy::$httpCode];
    }

    function curl_close($handle): void
    {
        CurlSpy::$closed = true;
    }
}
