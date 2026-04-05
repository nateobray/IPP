<?php

namespace obray\ipp\spec;

use obray\ipp\enums\JobState;
use obray\ipp\enums\Operations;
use obray\ipp\enums\PrinterState;
use obray\ipp\types\Charset;
use obray\ipp\types\Integer;
use obray\ipp\types\Keyword;
use obray\ipp\types\MimeMediaType;
use obray\ipp\types\NameWithLanguage;
use obray\ipp\types\NameWithoutLanguage;
use obray\ipp\types\NaturalLanguage;
use obray\ipp\types\URI;

final class Rfc2911AttributeMatrix
{
    public static function requiredJobDescriptionAttributes(): array
    {
        return [
            'job-uri' => [
                'section' => '4.3.1',
                'syntax' => 'uri',
                'multiple' => false,
                'sample' => 'ipp://printer.example/jobs/42',
                'value_classes' => [URI::class],
            ],
            'job-id' => [
                'section' => '4.3.2',
                'syntax' => 'integer(1:MAX)',
                'multiple' => false,
                'sample' => 42,
                'value_classes' => [Integer::class],
            ],
            'job-printer-uri' => [
                'section' => '4.3.3',
                'syntax' => 'uri',
                'multiple' => false,
                'sample' => 'ipp://printer.example/printers/main',
                'value_classes' => [URI::class],
            ],
            'job-name' => [
                'section' => '4.3.5',
                'syntax' => 'name(MAX)',
                'multiple' => false,
                'sample' => 'Quarterly report',
                'value_classes' => [NameWithoutLanguage::class, NameWithLanguage::class],
            ],
            'job-originating-user-name' => [
                'section' => '4.3.6',
                'syntax' => 'name(MAX)',
                'multiple' => false,
                'sample' => 'alice',
                'value_classes' => [NameWithoutLanguage::class, NameWithLanguage::class],
            ],
            'job-state' => [
                'section' => '4.3.7',
                'syntax' => 'type1 enum',
                'multiple' => false,
                'sample' => JobState::PENDING,
                'value_classes' => [\obray\ipp\enums\JobState::class],
            ],
            'job-state-reasons' => [
                'section' => '4.3.8',
                'syntax' => '1setOf type2 keyword',
                'multiple' => true,
                'sample' => ['none'],
                'value_classes' => [Keyword::class],
            ],
            'time-at-creation' => [
                'section' => '4.3.14',
                'syntax' => 'integer(MIN:MAX)',
                'multiple' => false,
                'sample' => 100,
                'value_classes' => [Integer::class],
            ],
            'time-at-processing' => [
                'section' => '4.3.15',
                'syntax' => 'integer(MIN:MAX)',
                'multiple' => false,
                'sample' => 110,
                'value_classes' => [Integer::class],
            ],
            'time-at-completed' => [
                'section' => '4.3.16',
                'syntax' => 'integer(MIN:MAX)',
                'multiple' => false,
                'sample' => 120,
                'value_classes' => [Integer::class],
            ],
            'job-printer-up-time' => [
                'section' => '4.3.17.1',
                'syntax' => 'integer(1:MAX)',
                'multiple' => false,
                'sample' => 121,
                'value_classes' => [Integer::class],
            ],
            'attributes-charset' => [
                'section' => '4.3.19',
                'syntax' => 'charset',
                'multiple' => false,
                'sample' => 'utf-8',
                'value_classes' => [Charset::class],
            ],
            'attributes-natural-language' => [
                'section' => '4.3.20',
                'syntax' => 'naturalLanguage',
                'multiple' => false,
                'sample' => 'en',
                'value_classes' => [NaturalLanguage::class],
            ],
        ];
    }

    public static function requiredPrinterDescriptionAttributes(): array
    {
        return [
            'printer-uri-supported' => [
                'section' => '4.4.1',
                'syntax' => '1setOf uri',
                'multiple' => true,
                'sample' => ['ipp://printer.example/printers/main'],
                'value_classes' => [URI::class],
            ],
            'uri-security-supported' => [
                'section' => '4.4.2',
                'syntax' => '1setOf type2 keyword',
                'multiple' => true,
                'sample' => ['none'],
                'value_classes' => [Keyword::class],
            ],
            'uri-authentication-supported' => [
                'section' => '4.4.3',
                'syntax' => '1setOf type2 keyword',
                'multiple' => true,
                'sample' => ['requesting-user-name'],
                'value_classes' => [Keyword::class],
            ],
            'printer-name' => [
                'section' => '4.4.4',
                'syntax' => 'name(127)',
                'multiple' => false,
                'sample' => 'Main printer',
                'value_classes' => [NameWithoutLanguage::class, NameWithLanguage::class],
            ],
            'printer-state' => [
                'section' => '4.4.11',
                'syntax' => 'type1 enum',
                'multiple' => false,
                'sample' => PrinterState::idle,
                'value_classes' => [\obray\ipp\enums\PrinterState::class],
            ],
            'printer-state-reasons' => [
                'section' => '4.4.12',
                'syntax' => '1setOf type2 keyword',
                'multiple' => true,
                'sample' => ['none'],
                'value_classes' => [Keyword::class],
            ],
            'ipp-versions-supported' => [
                'section' => '4.4.14',
                'syntax' => '1setOf type2 keyword',
                'multiple' => true,
                'sample' => ['1.1'],
                'value_classes' => [Keyword::class],
            ],
            'operations-supported' => [
                'section' => '4.4.15',
                'syntax' => '1setOf type2 enum',
                'multiple' => true,
                'sample' => [Operations::PRINT_JOB, Operations::GET_PRINTER_ATTRIBUTES],
                'value_classes' => [Operations::class],
            ],
            'charset-configured' => [
                'section' => '4.4.17',
                'syntax' => 'charset',
                'multiple' => false,
                'sample' => 'utf-8',
                'value_classes' => [Charset::class],
            ],
            'charset-supported' => [
                'section' => '4.4.18',
                'syntax' => '1setOf charset',
                'multiple' => true,
                'sample' => ['utf-8', 'us-ascii'],
                'value_classes' => [Charset::class],
            ],
            'natural-language-configured' => [
                'section' => '4.4.19',
                'syntax' => 'naturalLanguage',
                'multiple' => false,
                'sample' => 'en',
                'value_classes' => [NaturalLanguage::class],
            ],
            'generated-natural-language-supported' => [
                'section' => '4.4.20',
                'syntax' => '1setOf naturalLanguage',
                'multiple' => true,
                'sample' => ['en', 'fr-ca'],
                'value_classes' => [NaturalLanguage::class],
            ],
            'document-format-default' => [
                'section' => '4.4.21',
                'syntax' => 'mimeMediaType',
                'multiple' => false,
                'sample' => 'application/pdf',
                'value_classes' => [MimeMediaType::class],
            ],
            'document-format-supported' => [
                'section' => '4.4.22',
                'syntax' => '1setOf mimeMediaType',
                'multiple' => true,
                'sample' => ['application/pdf', 'application/postscript'],
                'value_classes' => [MimeMediaType::class],
            ],
            'printer-is-accepting-jobs' => [
                'section' => '4.4.23',
                'syntax' => 'boolean',
                'multiple' => false,
                'sample' => true,
                'value_classes' => [\obray\ipp\types\Boolean::class],
            ],
            'queued-job-count' => [
                'section' => '4.4.24',
                'syntax' => 'integer(0:MAX)',
                'multiple' => false,
                'sample' => 3,
                'value_classes' => [Integer::class],
            ],
            'pdl-override-supported' => [
                'section' => '4.4.28',
                'syntax' => 'type2 keyword',
                'multiple' => false,
                'sample' => 'not-attempted',
                'value_classes' => [Keyword::class],
            ],
            'printer-up-time' => [
                'section' => '4.4.29',
                'syntax' => 'integer(1:MAX)',
                'multiple' => false,
                'sample' => 12000,
                'value_classes' => [Integer::class],
            ],
            'compression-supported' => [
                'section' => '4.4.32',
                'syntax' => '1setOf type3 keyword',
                'multiple' => true,
                'sample' => ['none'],
                'value_classes' => [Keyword::class],
            ],
        ];
    }

    public static function requiredJobDescriptionAttributeNames(): array
    {
        return array_keys(self::requiredJobDescriptionAttributes());
    }

    public static function requiredPrinterDescriptionAttributeNames(): array
    {
        return array_keys(self::requiredPrinterDescriptionAttributes());
    }
}
