<?php
namespace obray\ipp\enums;

/**
 * PWG5101.1 — PWG Media Standardized Names 2.0
 *
 * String constants for standard media size keyword values used in the
 * `media` job-template attribute and `media-size` collection member.
 * The naming convention is: {type}_{name}_{width}x{height}{unit}
 */
class MediaSize
{
    // ISO A series
    const ISO_A0  = 'iso_a0_841x1189mm';
    const ISO_A1  = 'iso_a1_594x841mm';
    const ISO_A2  = 'iso_a2_420x594mm';
    const ISO_A3  = 'iso_a3_297x420mm';
    const ISO_A4  = 'iso_a4_210x297mm';
    const ISO_A5  = 'iso_a5_148x210mm';
    const ISO_A6  = 'iso_a6_105x148mm';
    const ISO_A7  = 'iso_a7_74x105mm';
    const ISO_A8  = 'iso_a8_52x74mm';
    const ISO_A9  = 'iso_a9_37x52mm';
    const ISO_A10 = 'iso_a10_26x37mm';

    // ISO B series
    const ISO_B0  = 'iso_b0_1000x1414mm';
    const ISO_B1  = 'iso_b1_707x1000mm';
    const ISO_B2  = 'iso_b2_500x707mm';
    const ISO_B3  = 'iso_b3_353x500mm';
    const ISO_B4  = 'iso_b4_250x353mm';
    const ISO_B5  = 'iso_b5_176x250mm';
    const ISO_B6  = 'iso_b6_125x176mm';
    const ISO_B7  = 'iso_b7_88x125mm';
    const ISO_B8  = 'iso_b8_62x88mm';
    const ISO_B9  = 'iso_b9_44x62mm';
    const ISO_B10 = 'iso_b10_31x44mm';

    // ISO C (envelope) series
    const ISO_C3 = 'iso_c3_324x458mm';
    const ISO_C4 = 'iso_c4_229x324mm';
    const ISO_C5 = 'iso_c5_162x229mm';
    const ISO_C6 = 'iso_c6_114x162mm';
    const ISO_DL = 'iso_dl_110x220mm';

    // JIS B series
    const JIS_B0  = 'jis_b0_1030x1456mm';
    const JIS_B1  = 'jis_b1_728x1030mm';
    const JIS_B2  = 'jis_b2_515x728mm';
    const JIS_B3  = 'jis_b3_364x515mm';
    const JIS_B4  = 'jis_b4_257x364mm';
    const JIS_B5  = 'jis_b5_182x257mm';
    const JIS_B6  = 'jis_b6_128x182mm';
    const JIS_B7  = 'jis_b7_91x128mm';
    const JIS_B8  = 'jis_b8_64x91mm';
    const JIS_B9  = 'jis_b9_45x64mm';
    const JIS_B10 = 'jis_b10_32x45mm';

    // North American sizes
    const NA_LETTER         = 'na_letter_8.5x11in';
    const NA_LEGAL          = 'na_legal_8.5x14in';
    const NA_LEDGER         = 'na_ledger_11x17in';
    const NA_TABLOID        = 'na_tabloid_11x17in';
    const NA_INVOICE        = 'na_invoice_5.5x8.5in';
    const NA_EXECUTIVE      = 'na_executive_7.25x10.5in';
    const NA_GOVT_LETTER    = 'na_govt-letter_8x10in';
    const NA_GOVT_LEGAL     = 'na_govt-legal_8x13in';
    const NA_QUARTO         = 'na_quarto_8.5x10.83in';
    const NA_FOOLSCAP       = 'na_foolscap_8.5x13in';
    const NA_LETTER_PLUS    = 'na_letter-plus_8.5x12.69in';
    const NA_SUPER_B        = 'na_super-b_13x19in';
    const NA_ARCH_A         = 'na_arch-a_9x12in';
    const NA_ARCH_B         = 'na_arch-b_12x18in';
    const NA_ARCH_C         = 'na_arch-c_18x24in';
    const NA_ARCH_D         = 'na_arch-d_24x36in';
    const NA_ARCH_E         = 'na_arch-e_36x48in';
    const NA_ARCH_E2        = 'na_arch-e2_26x38in';
    const NA_ARCH_E3        = 'na_arch-e3_27x39in';

    // North American envelopes
    const NA_NUMBER_9_ENVELOPE  = 'na_number-9_3.875x8.875in';
    const NA_NUMBER_10_ENVELOPE = 'na_number-10_4.125x9.5in';
    const NA_NUMBER_11_ENVELOPE = 'na_number-11_4.5x10.375in';
    const NA_NUMBER_12_ENVELOPE = 'na_number-12_4.75x11in';
    const NA_NUMBER_14_ENVELOPE = 'na_number-14_5x11.5in';
    const NA_6X9_ENVELOPE       = 'na_6x9_6x9in';
    const NA_7X9_ENVELOPE       = 'na_7x9_7x9in';
    const NA_9X11_ENVELOPE      = 'na_9x11_9x11in';
    const NA_10X13_ENVELOPE     = 'na_10x13_10x13in';
    const NA_10X14_ENVELOPE     = 'na_10x14_10x14in';
    const NA_10X15_ENVELOPE     = 'na_10x15_10x15in';
    const NA_MONARCH_ENVELOPE   = 'na_monarch_3.875x7.5in';
    const NA_PERSONAL_ENVELOPE  = 'na_personal_3.625x6.5in';

    // Photo/index card sizes
    const NA_INDEX_3X5     = 'na_index-3x5_3x5in';
    const NA_INDEX_4X6     = 'na_index-4x6_4x6in';
    const NA_INDEX_4X6_EXT = 'na_index-4x6-ext_6x8in';
    const NA_INDEX_5X8     = 'na_index-5x8_5x8in';

    // Japanese sizes
    const JPN_CHOU2  = 'jpn_chou2_111.1x146mm';
    const JPN_CHOU3  = 'jpn_chou3_120x235mm';
    const JPN_CHOU4  = 'jpn_chou4_90x205mm';
    const JPN_HAGAKI = 'jpn_hagaki_100x148mm';
    const JPN_KAHU   = 'jpn_kahu_240x322.1mm';
    const JPN_KAKU2  = 'jpn_kaku2_240x332mm';
    const JPN_OUFUKU = 'jpn_oufuku_148x200mm';
    const JPN_YOU4   = 'jpn_you4_105x235mm';

    // Other common sizes
    const OM_ITALIAN        = 'om_italian_110x230mm';
    const OM_LARGE_PHOTO    = 'om_large-photo_200x300mm';
    const OM_FOLIO          = 'om_folio_210x330mm';
    const OM_FOLIO_SP       = 'om_folio-sp_215x315mm';
    const OM_INVITE         = 'om_invite_220x220mm';
    const OM_SMALL_PHOTO    = 'om_small-photo_100x150mm';
    const OM_WIDE_PHOTO     = 'om_wide-photo_100x200mm';
    const OM_POSTFIX        = 'om_postfix_114x229mm';

    // Roll/continuous media
    const NA_WIDE_FORMAT    = 'na_wide-format_30x42in';
}
