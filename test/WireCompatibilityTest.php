<?php
declare(strict_types=1);

require_once __DIR__ . '/RequestStub.php';

use obray\ipp\Printer;
use obray\ipp\transport\IPPPayload;
use PHPUnit\Framework\TestCase;

final class WireCompatibilityTest extends TestCase
{
    // Build wire bytes independently of the library encoder (RFC 8010 §3).
    private static function attribute(int $tag, string $name, string $value): string
    {
        return pack('Cn', $tag, strlen($name)) . $name . pack('n', strlen($value)) . $value;
    }

    private static function operationGroup(): string
    {
        return "\x01"
            . self::attribute(0x47, 'attributes-charset', 'utf-8')
            . self::attribute(0x48, 'attributes-natural-language', 'en');
    }

    public function testSuccessfulPrintResponseWithSubstitutionRetainsJobId(): void
    {
        // RFC 8011 §4.2.1.2: operation, unsupported, then job attributes.
        $binary = pack('CCnN', 1, 1, 0x0001, 42) . self::operationGroup()
            . "\x05" . self::attribute(0x44, 'media', 'unsupported-paper')
            . "\x02" . self::attribute(0x21, 'job-id', pack('N', 123))
            . self::attribute(0x45, 'job-uri', 'ipp://printer.example/jobs/123')
            . self::attribute(0x23, 'job-state', pack('N', 3))
            . self::attribute(0x44, 'job-state-reasons', 'none') . "\x03";

        $payload = new IPPPayload();
        $payload->decode($binary);

        $this->assertSame(0x0001, $payload->statusCode->getValue());
        $this->assertSame(42, $payload->requestId->getValue());
        $this->assertSame(123, $payload->jobAttributes[0]->{'job-id'}->getAttributeValue());
        $this->assertSame('unsupported-paper', (string) $payload->unsupportedAttributes[0]->media);
    }

    public function testUnsupportedRequestedAttributesCanPrecedePrinterAttributes(): void
    {
        // RFC 8011 §4.2.5.2 uses the same ordering for printer responses.
        $binary = pack('CCnN', 1, 1, 0x0000, 43) . self::operationGroup()
            . "\x05" . self::attribute(0x44, 'requested-attributes', 'vendor-feature')
            . "\x04" . self::attribute(0x42, 'printer-name', 'Office printer') . "\x03";

        $payload = new IPPPayload();
        $payload->decode($binary);

        $this->assertSame('Office printer', (string) $payload->printerAttributes[0]->{'printer-name'});
        $this->assertSame('vendor-feature', (string) $payload->unsupportedAttributes[0]->{'requested-attributes'});
    }

    public function testUnsupportedAttributesCanPrecedeMultipleJobs(): void
    {
        $binary = pack('CCnN', 1, 1, 0, 44) . self::operationGroup()
            . "\x05" . self::attribute(0x44, 'requested-attributes', 'vendor-feature')
            . "\x02" . self::attribute(0x21, 'job-id', pack('N', 123))
            . "\x02" . self::attribute(0x21, 'job-id', pack('N', 124)) . "\x03";

        $payload = new IPPPayload();
        $payload->decode($binary);

        $this->assertCount(2, $payload->jobAttributes);
        $this->assertSame(123, $payload->jobAttributes[0]->{'job-id'}->getAttributeValue());
        $this->assertSame(124, $payload->jobAttributes[1]->{'job-id'}->getAttributeValue());
    }

    public function testReusingDecoderDoesNotRetainPreviousResponseGroupsOrDocument(): void
    {
        $header = pack('CCnN', 1, 1, 0, 45) . self::operationGroup();
        $payload = new IPPPayload();
        $payload->decode($header . "\x02" . self::attribute(0x21, 'job-id', pack('N', 123)) . "\x03old document");
        $payload->decode($header . "\x03");

        $this->assertNull($payload->jobAttributes);
        $document = new \ReflectionProperty(IPPPayload::class, 'document');
        $document->setAccessible(true);
        $this->assertNull($document->getValue($payload));
    }

    /** @dataProvider identifyOptions */
    public function testIdentifyPrinterEncodesEveryOption(?array $actions, ?string $message): void
    {
        $uri = 'ipp://printer.example/ipp/print';
        $printer = new Printer($uri, '', '', [], RequestStub::class);
        $printer->identifyPrinter(23, $actions, $message);

        // PWG 5100.13: actions are 1setOf keyword, message is text(127).
        $expected = pack('CCnN', 2, 0, 0x003c, 23) . self::operationGroup()
            . self::attribute(0x45, 'printer-uri', $uri);
        foreach ($actions ?? [] as $index => $action) {
            $expected .= self::attribute(0x44, $index === 0 ? 'identify-actions' : '', $action);
        }
        if ($message !== null) {
            $expected .= self::attribute(0x41, 'message', $message);
        }
        $expected .= "\x03";

        $this->assertSame(bin2hex($expected), bin2hex(RequestStub::$last['encodedPayload']));
    }

    public function identifyOptions(): array
    {
        return [
            'all options' => [['flash', 'sound'], 'Hello'],
            'actions only' => [['flash'], null],
            'message only' => [null, 'Prêt'],
            'empty message' => [null, ''],
            'defaults' => [null, null],
        ];
    }
}
