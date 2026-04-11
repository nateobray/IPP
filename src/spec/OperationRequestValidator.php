<?php

namespace obray\ipp\spec;

use obray\ipp\JobAttributes;
use obray\ipp\OperationAttributes;
use obray\ipp\exceptions\InvalidRequest;
use obray\ipp\types\OctetString;
use obray\ipp\types\Operation;

final class OperationRequestValidator
{
    public static function validate(
        int $operationCode,
        OperationAttributes $operationAttributes,
        ?JobAttributes $jobAttributes = null,
        ?OctetString $document = null
    ): void {
        $requirements = self::requirements()[$operationCode] ?? self::defaultRequirements($operationCode);

        $operationAttributes->validate(array_keys($operationAttributes->jsonSerialize()));

        $matchedTargetPrefix = self::matchedTargetPrefix(
            $operationAttributes,
            $requirements['target_prefixes']
        );

        if ($matchedTargetPrefix === null) {
            throw new InvalidRequest(sprintf(
                '%s requires leading target attributes in one of these forms: %s.',
                self::operationName($operationCode),
                self::describeAttributeAlternatives($requirements['target_prefixes'])
            ));
        }

        self::assertRequiredAttributesPresent(
            $operationAttributes,
            $requirements['required_operation_attributes'],
            'operation',
            $operationCode
        );
        self::assertForbiddenAttributesAbsent(
            $operationAttributes,
            $requirements['forbidden_operation_attributes'],
            $operationCode
        );
        self::assertDocumentExpectation(
            $operationCode,
            $requirements['document_requirement'],
            $document
        );

        if (!empty($requirements['requires_last_document_true_without_document'])
            && $document === null
            && self::attributeValue($operationAttributes, 'last-document') !== 'true'
        ) {
            throw new InvalidRequest(
                self::operationName($operationCode) . ' without document data requires "last-document" to be true.'
            );
        }

        if ($jobAttributes !== null && !empty($requirements['forbidden_job_attributes'])) {
            self::assertForbiddenAttributesAbsent(
                $jobAttributes,
                $requirements['forbidden_job_attributes'],
                $operationCode,
                'job'
            );
        }
    }

    private static function requirements(): array
    {
        return [
            Operation::PRINT_JOB => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'required',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::PRINT_URI => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => ['document-uri'],
                'forbidden_operation_attributes' => ['last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::VALIDATE_JOB => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::CREATE_JOB => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => [
                    'document-name',
                    'document-format',
                    'compression',
                    'document-natural-language',
                    'document-uri',
                    'last-document',
                ],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::GET_PRINTER_ATTRIBUTES => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::GET_JOBS => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::PAUSE_PRINTER => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::RESUME_PRINTER => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::PURGE_JOBS => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::GET_PRINTER_SUPPORTED_VALUES => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::GET_RESOURCE_ATTRIBUTES => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::GET_RESOURCE_DATA => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::GET_RESOURCES => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::CANCEL_JOBS => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::CANCEL_MY_JOBS => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::SEND_DOCUMENT => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => ['last-document'],
                'forbidden_operation_attributes' => ['document-uri'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'optional',
                'requires_last_document_true_without_document' => true,
            ],
            Operation::SEND_URI => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_operation_attributes' => [],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::CANCEL_JOB => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::GET_JOB_ATTRIBUTES => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => [
                    'document-uri',
                    'document-format',
                    'document-name',
                    'document-natural-language',
                    'compression',
                    'last-document',
                ],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::HOLD_JOB => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::RELEASE_JOB => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::RESTART_JOB => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::CLOSE_JOB => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::SET_PRINTER_ATTRIBUTES => [
                'target_prefixes' => [['printer-uri']],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
            Operation::SET_JOB_ATTRIBUTES => [
                'target_prefixes' => [
                    ['printer-uri', 'job-id'],
                    ['job-uri'],
                ],
                'required_operation_attributes' => [],
                'forbidden_operation_attributes' => ['document-uri', 'last-document'],
                'forbidden_job_attributes' => [],
                'document_requirement' => 'forbidden',
                'requires_last_document_true_without_document' => false,
            ],
        ];
    }

    private static function defaultRequirements(int $operationCode): array
    {
        return [
            'target_prefixes' => [['printer-uri']],
            'required_operation_attributes' => [],
            'forbidden_operation_attributes' => [],
            'forbidden_job_attributes' => [],
            'document_requirement' => 'forbidden',
            'requires_last_document_true_without_document' => false,
        ];
    }

    private static function matchedTargetPrefix(
        OperationAttributes $operationAttributes,
        array $targetPrefixes
    ): ?array {
        $attributeNames = array_keys($operationAttributes->jsonSerialize());

        if (($attributeNames[0] ?? null) !== 'attributes-charset'
            || ($attributeNames[1] ?? null) !== 'attributes-natural-language'
        ) {
            return null;
        }

        foreach ($targetPrefixes as $targetPrefix) {
            if (array_slice($attributeNames, 2, count($targetPrefix)) === $targetPrefix
                && self::allAttributesPopulated($operationAttributes, $targetPrefix)
            ) {
                return $targetPrefix;
            }
        }

        return null;
    }

    private static function allAttributesPopulated($attributeGroup, array $attributeNames): bool
    {
        foreach ($attributeNames as $attributeName) {
            if (!self::attributeIsPopulated($attributeGroup, $attributeName)) {
                return false;
            }
        }

        return true;
    }

    private static function assertRequiredAttributesPresent(
        $attributeGroup,
        array $attributeNames,
        string $groupName,
        int $operationCode
    ): void {
        foreach ($attributeNames as $attributeName) {
            if (!self::attributeIsPopulated($attributeGroup, $attributeName)) {
                throw new InvalidRequest(sprintf(
                    '%s requires %s attribute "%s".',
                    self::operationName($operationCode),
                    $groupName,
                    $attributeName
                ));
            }
        }
    }

    private static function assertForbiddenAttributesAbsent(
        $attributeGroup,
        array $attributeNames,
        int $operationCode,
        string $groupName = 'operation'
    ): void {
        foreach ($attributeNames as $attributeName) {
            if (array_key_exists($attributeName, $attributeGroup->jsonSerialize())) {
                throw new InvalidRequest(sprintf(
                    '%s forbids %s attribute "%s".',
                    self::operationName($operationCode),
                    $groupName,
                    $attributeName
                ));
            }
        }
    }

    private static function assertDocumentExpectation(
        int $operationCode,
        string $documentRequirement,
        ?OctetString $document
    ): void {
        if ($documentRequirement === 'required' && $document === null) {
            throw new InvalidRequest(self::operationName($operationCode) . ' requires document data.');
        }

        if ($documentRequirement === 'forbidden' && $document !== null) {
            throw new InvalidRequest(self::operationName($operationCode) . ' does not allow document data.');
        }
    }

    private static function attributeIsPopulated($attributeGroup, string $attributeName): bool
    {
        if (!array_key_exists($attributeName, $attributeGroup->jsonSerialize())) {
            return false;
        }

        $value = $attributeGroup->{$attributeName};

        if (is_array($value)) {
            if ($value === []) {
                return false;
            }

            foreach ($value as $item) {
                if (trim((string) $item) !== '') {
                    return true;
                }
            }

            return false;
        }

        return trim((string) $value) !== '';
    }

    private static function attributeValue(OperationAttributes $operationAttributes, string $attributeName): ?string
    {
        if (!array_key_exists($attributeName, $operationAttributes->jsonSerialize())) {
            return null;
        }

        return strtolower(trim((string) $operationAttributes->{$attributeName}));
    }

    private static function describeAttributeAlternatives(array $alternatives): string
    {
        return implode(' or ', array_map(static function (array $attributes): string {
            return implode(', ', array_merge(
                ['attributes-charset', 'attributes-natural-language'],
                $attributes
            ));
        }, $alternatives));
    }

    private static function operationName(int $operationCode): string
    {
        return match ($operationCode) {
            Operation::PRINT_JOB => 'Print-Job',
            Operation::PRINT_URI => 'Print-URI',
            Operation::VALIDATE_JOB => 'Validate-Job',
            Operation::CREATE_JOB => 'Create-Job',
            Operation::SEND_DOCUMENT => 'Send-Document',
            Operation::SEND_URI => 'Send-URI',
            Operation::CANCEL_JOB => 'Cancel-Job',
            Operation::GET_JOB_ATTRIBUTES => 'Get-Job-Attributes',
            Operation::GET_JOBS => 'Get-Jobs',
            Operation::GET_PRINTER_ATTRIBUTES => 'Get-Printer-Attributes',
            Operation::HOLD_JOB => 'Hold-Job',
            Operation::RELEASE_JOB => 'Release-Job',
            Operation::RESTART_JOB => 'Restart-Job',
            Operation::PAUSE_PRINTER => 'Pause-Printer',
            Operation::RESUME_PRINTER => 'Resume-Printer',
            Operation::PURGE_JOBS => 'Purge-Jobs',
            Operation::GET_PRINTER_SUPPORTED_VALUES => 'Get-Printer-Supported-Values',
            Operation::GET_RESOURCE_ATTRIBUTES => 'Get-Resource-Attributes',
            Operation::GET_RESOURCE_DATA => 'Get-Resource-Data',
            Operation::GET_RESOURCES => 'Get-Resources',
            Operation::CANCEL_JOBS => 'Cancel-Jobs',
            Operation::CANCEL_MY_JOBS => 'Cancel-My-Jobs',
            Operation::CLOSE_JOB => 'Close-Job',
            Operation::SET_PRINTER_ATTRIBUTES => 'Set-Printer-Attributes',
            Operation::SET_JOB_ATTRIBUTES => 'Set-Job-Attributes',
            default => sprintf('IPP operation 0x%04x', $operationCode),
        };
    }
}
