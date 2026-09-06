<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PwgMediaTest extends TestCase
{
    public function testCanonicalizeMapsCommonAliasesToStandardizedNames(): void
    {
        $this->assertSame('iso_a4_210x297mm', \obray\ipp\pwg\Media::canonicalize('A4'));
        $this->assertSame('na_letter_8.5x11in', \obray\ipp\pwg\Media::canonicalize('letter'));
        $this->assertSame('na_index-4x6_4x6in', \obray\ipp\pwg\Media::canonicalize('4x6'));
    }

    public function testIsStandardizedNameRecognizesCommonSupportedNames(): void
    {
        $this->assertTrue(\obray\ipp\pwg\Media::isStandardizedName('iso_a4_210x297mm'));
        $this->assertTrue(\obray\ipp\pwg\Media::isStandardizedName('A3'));
        $this->assertFalse(\obray\ipp\pwg\Media::isStandardizedName('custom-tray-stock'));
    }
}
