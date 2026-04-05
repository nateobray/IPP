<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class Rfc2911RequiredAttributesTest extends TestCase
{
    public function testJobAttributesEncodeAndDecodeAllRequiredRfc2911JobDescriptionAttributes(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();

        foreach (\obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributes() as $name => $definition) {
            $jobAttributes->{$name} = $definition['sample'];
        }

        $decoded = new \obray\ipp\JobAttributes();
        $offset = 0;
        $decoded->decode($jobAttributes->encode(), $offset);

        foreach (\obray\ipp\spec\Rfc2911AttributeMatrix::requiredJobDescriptionAttributes() as $name => $definition) {
            $this->assertTrue($decoded->has($name), sprintf('Missing required job attribute "%s"', $name));
            $this->assertAttributeMatchesDefinition($decoded->{$name}, $definition, $name);
        }
    }

    public function testPrinterAttributesEncodeAndDecodeAllRequiredRfc2911PrinterDescriptionAttributes(): void
    {
        $printerAttributes = new \obray\ipp\PrinterAttributes();

        foreach (\obray\ipp\spec\Rfc2911AttributeMatrix::requiredPrinterDescriptionAttributes() as $name => $definition) {
            $printerAttributes->{$name} = $definition['sample'];
        }

        $decoded = new \obray\ipp\PrinterAttributes();
        $offset = 0;
        $decoded->decode($printerAttributes->encode(), $offset);

        foreach (\obray\ipp\spec\Rfc2911AttributeMatrix::requiredPrinterDescriptionAttributes() as $name => $definition) {
            $this->assertTrue($decoded->has($name), sprintf('Missing required printer attribute "%s"', $name));
            $this->assertAttributeMatchesDefinition($decoded->{$name}, $definition, $name);
        }
    }

    private function assertAttributeMatchesDefinition($attribute, array $definition, string $name): void
    {
        if ($definition['multiple']) {
            $items = is_array($attribute) ? $attribute : [$attribute];
            $this->assertNotEmpty($items, sprintf('Expected "%s" to contain at least one value', $name));

            foreach ($items as $item) {
                $this->assertAllowedValueClass($item->getAttributeValueClass(), $definition['value_classes'], $name);
            }

            return;
        }

        $this->assertAllowedValueClass($attribute->getAttributeValueClass(), $definition['value_classes'], $name);
    }

    private function assertAllowedValueClass(object $value, array $allowedClasses, string $name): void
    {
        foreach ($allowedClasses as $allowedClass) {
            if ($value instanceof $allowedClass) {
                return;
            }
        }

        $this->fail(sprintf(
            'Attribute "%s" decoded to unexpected value class %s.',
            $name,
            $value::class
        ));
    }
}
