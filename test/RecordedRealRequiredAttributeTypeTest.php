<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class RecordedRealRequiredAttributeTypeTest extends TestCase
{
    public static function getPrinterAttributesFixtureProvider(): array
    {
        return self::fixtureProvider('get-printer-attributes');
    }

    public static function getJobAttributesFixtureProvider(): array
    {
        return self::fixtureProvider('get-job-attributes');
    }

    /**
     * @dataProvider getPrinterAttributesFixtureProvider
     */
    public function testRecordedPrinterDescriptionResponsesDecodeRequiredAttributesToExpectedTypes(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $printerAttributes = $this->firstGroup($fixture['response']->printerAttributes);

        $this->assertNotNull($printerAttributes);

        foreach (\obray\ipp\spec\Rfc2911AttributeMatrix::requiredPrinterDescriptionAttributes() as $name => $definition) {
            $this->assertTrue($printerAttributes->has($name), sprintf('Expected "%s" in %s', $name, $metaPath));
            $this->assertAttributeMatchesDefinition($printerAttributes->{$name}, $definition, $name, $metaPath);
        }
    }

    /**
     * @dataProvider getJobAttributesFixtureProvider
     */
    public function testRecordedJobDescriptionResponsesDecodeRequiredAttributesToExpectedTypes(string $metaPath): void
    {
        $fixture = $this->loadFixture($metaPath);
        $requestedAttributes = $this->attributeValues($fixture['request']->operationAttributes->{'requested-attributes'});
        $this->assertSame(\obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributeNames(), $requestedAttributes);

        $jobAttributes = $this->firstGroup($fixture['response']->jobAttributes);
        $this->assertNotNull($jobAttributes);

        $requiredLiveAttributes = array_diff(
            \obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributeNames(),
            ['attributes-charset', 'attributes-natural-language']
        );

        foreach ($requiredLiveAttributes as $name) {
            $this->assertTrue($jobAttributes->has($name), sprintf('Expected "%s" in %s', $name, $metaPath));
            $definition = \obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributes()[$name];
            $this->assertAttributeMatchesDefinition($jobAttributes->{$name}, $definition, $name, $metaPath);
        }

        foreach (\obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributes() as $name => $definition) {
            if (!$jobAttributes->has($name)) {
                continue;
            }

            $this->assertAttributeMatchesDefinition($jobAttributes->{$name}, $definition, $name, $metaPath);
        }
    }

    private static function fixtureProvider(string $operation): array
    {
        $fixtureFiles = glob(__DIR__ . '/fixtures/real/*/*/' . $operation . '.meta.json') ?: [];
        sort($fixtureFiles);

        $data = [];
        foreach ($fixtureFiles as $fixtureFile) {
            $data[basename(dirname($fixtureFile)) . ' ' . $operation] = [$fixtureFile];
        }

        return $data;
    }

    private function loadFixture(string $metaPath): array
    {
        $meta = json_decode((string) file_get_contents($metaPath), true, 512, JSON_THROW_ON_ERROR);
        $requestBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['request_file']);
        $responseBinary = (string) file_get_contents(dirname($metaPath) . '/' . $meta['response_file']);

        $request = new \obray\ipp\transport\IPPPayload();
        $request->decode($this->requestToResponseBinary($requestBinary));

        $response = new \obray\ipp\transport\IPPPayload();
        $response->decode($responseBinary);

        return [
            'meta' => $meta,
            'request' => $request,
            'response' => $response,
        ];
    }

    private function requestToResponseBinary(string $requestBinary): string
    {
        return substr($requestBinary, 0, 2)
            . pack('n', \obray\ipp\types\StatusCode::successful_ok)
            . substr($requestBinary, 4);
    }

    private function firstGroup($groups): ?\obray\ipp\AttributeGroup
    {
        if ($groups instanceof \obray\ipp\AttributeGroup) {
            return $groups;
        }

        if (!is_array($groups) || $groups === []) {
            return null;
        }

        $first = reset($groups);

        return $first instanceof \obray\ipp\AttributeGroup ? $first : null;
    }

    private function attributeValues($attribute): array
    {
        if (!is_array($attribute)) {
            return [(string) $attribute];
        }

        return array_map(static fn ($value) => (string) $value, $attribute);
    }

    private function assertAttributeMatchesDefinition($attribute, array $definition, string $name, string $metaPath): void
    {
        if ($definition['multiple']) {
            $items = is_array($attribute) ? $attribute : [$attribute];
            $this->assertNotEmpty($items, sprintf('Expected "%s" to have at least one value in %s', $name, $metaPath));

            foreach ($items as $item) {
                $this->assertAllowedValueClass($item->getAttributeValueClass(), $definition['value_classes'], $name, $metaPath);
            }

            return;
        }

        $this->assertAllowedValueClass($attribute->getAttributeValueClass(), $definition['value_classes'], $name, $metaPath);
    }

    private function assertAllowedValueClass(object $value, array $allowedClasses, string $name, string $metaPath): void
    {
        if ($value instanceof \obray\ipp\types\NoVal || $value instanceof \obray\ipp\types\Unknown) {
            return;
        }

        foreach ($allowedClasses as $allowedClass) {
            if ($value instanceof $allowedClass) {
                return;
            }
        }

        $this->fail(sprintf(
            'Attribute "%s" in %s decoded to unexpected value class %s.',
            $name,
            $metaPath,
            $value::class
        ));
    }
}
