<?php

namespace obray\ipp\pwg;

final class Media
{
    public const NA_LETTER_8_5X11IN = 'na_letter_8.5x11in';
    public const NA_LEGAL_8_5X14IN = 'na_legal_8.5x14in';
    public const ISO_A4_210X297MM = 'iso_a4_210x297mm';
    public const ISO_A3_297X420MM = 'iso_a3_297x420mm';
    public const NA_INDEX_4X6_4X6IN = 'na_index-4x6_4x6in';
    public const OE_PHOTO_L_3_5X5IN = 'oe_photo-l_3.5x5in';

    private const ALIASES = [
        'letter' => self::NA_LETTER_8_5X11IN,
        'us-letter' => self::NA_LETTER_8_5X11IN,
        'na_letter_8.5x11' => self::NA_LETTER_8_5X11IN,
        'legal' => self::NA_LEGAL_8_5X14IN,
        'us-legal' => self::NA_LEGAL_8_5X14IN,
        'a4' => self::ISO_A4_210X297MM,
        'iso_a4' => self::ISO_A4_210X297MM,
        'a3' => self::ISO_A3_297X420MM,
        'iso_a3' => self::ISO_A3_297X420MM,
        '4x6' => self::NA_INDEX_4X6_4X6IN,
        'photo-l' => self::OE_PHOTO_L_3_5X5IN,
    ];

    private const COMMON_NAMES = [
        self::NA_LETTER_8_5X11IN,
        self::NA_LEGAL_8_5X14IN,
        self::ISO_A4_210X297MM,
        self::ISO_A3_297X420MM,
        self::NA_INDEX_4X6_4X6IN,
        self::OE_PHOTO_L_3_5X5IN,
    ];

    public static function commonNames(): array
    {
        return self::COMMON_NAMES;
    }

    public static function canonicalize(string $value): string
    {
        $normalized = strtolower(trim($value));

        return self::ALIASES[$normalized] ?? $normalized;
    }

    public static function isStandardizedName(string $value): bool
    {
        return in_array(self::canonicalize($value), self::COMMON_NAMES, true);
    }
}
