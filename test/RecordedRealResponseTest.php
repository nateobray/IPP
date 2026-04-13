<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use obray\ipp\test\support\RealFixtureSummary;
use PHPUnit\Framework\TestCase;

final class RecordedRealResponseTest extends TestCase
{
    public static function fixtureProvider(): array
    {
        $fixtureFiles = glob(__DIR__ . '/fixtures/real/*/*/*.meta.json') ?: [];
        sort($fixtureFiles);

        $data = [];
        foreach ($fixtureFiles as $fixtureFile) {
            $data[basename(dirname($fixtureFile)) . ' ' . basename($fixtureFile, '.meta.json')] = [$fixtureFile];
        }

        return $data;
    }

    /**
     * @dataProvider fixtureProvider
     */
    public function testRecordedResponseFixtureDecodesToExpectedSummary(string $metaPath): void
    {
        $meta = json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
        $responsePath = dirname($metaPath) . '/' . $meta['response_file'];
        $requestPath = dirname($metaPath) . '/' . $meta['request_file'];

        $this->assertFileExists($requestPath);
        $this->assertFileExists($responsePath);

        if (($meta['kind'] ?? 'ipp-response') !== 'ipp-response') {
            $this->assertArrayHasKey('http', $meta);
            $this->assertArrayHasKey('status', $meta['http']);
            return;
        }

        $responsePayload = new \obray\ipp\transport\IPPPayload();
        $responsePayload->decode((string) file_get_contents($responsePath));

        $actualSummary = RealFixtureSummary::fromPayload($responsePayload);
        $expectedSummary = $meta['summary'];

        $projectedActualSummary = [];
        foreach ($expectedSummary as $key => $value) {
            $projectedActualSummary[$key] = $actualSummary[$key] ?? null;
        }

        $this->assertSame($expectedSummary, $projectedActualSummary);
    }

    public function testRecordedResponseFixturesExistOrExplicitlySkip(): void
    {
        $fixtureFiles = glob(__DIR__ . '/fixtures/real/*/*/*.meta.json') ?: [];

        if ($fixtureFiles === []) {
            $this->markTestSkipped('No recorded real-device fixtures exist yet. Run `composer record:fixtures` on a machine with reachable printers.');
        }

        $this->assertNotEmpty($fixtureFiles);
    }
}
