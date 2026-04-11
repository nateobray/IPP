<?php
declare(strict_types=1);

$loader = require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    // --- Constructor type inference ---

    public function testConstructorInfersStringAsKeyword(): void
    {
        $col = new \obray\ipp\types\Collection(['media-source' => 'auto']);
        $this->assertInstanceOf(\obray\ipp\types\Keyword::class, $col->{'media-source'});
        $this->assertSame('auto', (string) $col->{'media-source'});
    }

    public function testConstructorInfersIntAsInteger(): void
    {
        $col = new \obray\ipp\types\Collection(['copies' => 3]);
        $this->assertInstanceOf(\obray\ipp\types\Integer::class, $col->{'copies'});
        $this->assertSame(3, $col->{'copies'}->getValue());
    }

    public function testConstructorInfersBoolAsBoolean(): void
    {
        $col = new \obray\ipp\types\Collection(['color' => true]);
        $this->assertInstanceOf(\obray\ipp\types\Boolean::class, $col->{'color'});
    }

    public function testConstructorAcceptsTypedValueArray(): void
    {
        $col = new \obray\ipp\types\Collection([
            'sides' => ['value' => 'two-sided-long-edge', 'type' => 'keyword'],
        ]);
        $this->assertSame('two-sided-long-edge', (string) $col->{'sides'});
    }

    public function testConstructorWithNullProducesEmptyCollection(): void
    {
        $col = new \obray\ipp\types\Collection();
        $binary = $col->encode();
        // Should produce just the end-collection tag (0x37)
        $this->assertSame('37', bin2hex($binary));
    }

    // --- encode() correctness ---

    public function testEncodeProducesNonEmptyBinary(): void
    {
        // Previously crashed with undefined len() — now uses strlen()
        $col = new \obray\ipp\types\Collection(['media-source' => 'envelope']);
        $binary = $col->encode();
        $this->assertIsString($binary);
        $this->assertNotEmpty($binary);
    }

    public function testEncodeEndsWithEndCollectionTag(): void
    {
        $col = new \obray\ipp\types\Collection(['key' => 'value']);
        $binary = $col->encode();
        // Last byte must be the end-collection tag 0x37
        $this->assertSame('37', bin2hex(substr($binary, -1)));
    }

    public function testGetLengthMatchesBinaryLength(): void
    {
        $col = new \obray\ipp\types\Collection(['media-source' => 'auto', 'copies' => 2]);
        $this->assertSame(strlen($col->encode()), $col->getLength());
    }

    // --- __get accessor ---

    public function testGetAccessorReturnsAttributeSetViaSetter(): void
    {
        $col = new \obray\ipp\types\Collection();
        $col->set('media-source', new \obray\ipp\types\Keyword('tray-1'));
        $this->assertSame('tray-1', (string) $col->{'media-source'});
    }

    public function testGetAccessorThrowsForMissingAttribute(): void
    {
        $col = new \obray\ipp\types\Collection();
        $this->expectException(\Exception::class);
        $col->{'nonexistent'};
    }

    // --- encode/decode round-trip via CollectionAttribute ---

    public function testRoundTripViaCollectionAttribute(): void
    {
        $input = [
            'media-source' => 'envelope',
            'copies'       => 2,
            'sides'        => 'two-sided-long-edge',
        ];

        $encoded = (new \obray\ipp\CollectionAttribute('media-col', $input))->encode();
        $decoded = (new \obray\ipp\CollectionAttribute())->decode($encoded);

        $this->assertSame('envelope', $decoded->getValue()['media-source']);
        $this->assertSame(2,          $decoded->getValue()['copies']);
        $this->assertSame('two-sided-long-edge', $decoded->getValue()['sides']);
    }

    public function testRoundTripPreservesLength(): void
    {
        $input = ['media-source' => 'auto', 'copies' => 1];
        $original = new \obray\ipp\CollectionAttribute('media-col', $input);
        $binary   = $original->encode();
        $decoded  = (new \obray\ipp\CollectionAttribute())->decode($binary);

        $this->assertSame($original->getLength(), $decoded->getLength());
    }

    // --- getValueTag ---

    public function testGetValueTagReturnsCollectionTag(): void
    {
        $col = new \obray\ipp\types\Collection();
        $this->assertSame(0x34, $col->getValueTag());
    }
}
