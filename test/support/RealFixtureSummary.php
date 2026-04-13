<?php
declare(strict_types=1);

namespace obray\ipp\test\support;

final class RealFixtureSummary
{
    public static function fromPayload(\obray\ipp\transport\IPPPayload $payload): array
    {
        return [
            'version' => (string) $payload->versionNumber,
            'request_id' => $payload->requestId?->getValue(),
            'status' => (string) $payload->statusCode,
            'operation_attribute_names' => self::attributeNames($payload->operationAttributes),
            'job_attribute_group_count' => self::groupCount($payload->jobAttributes),
            'document_attribute_group_count' => self::groupCount($payload->documentAttributes),
            'printer_attribute_group_count' => self::groupCount($payload->printerAttributes),
            'unsupported_attribute_group_count' => self::groupCount($payload->unsupportedAttributes),
            'printer_names' => self::collectValues($payload->printerAttributes, 'printer-name'),
            'job_ids' => self::collectValues($payload->jobAttributes, 'job-id'),
            'job_states' => self::collectValues($payload->jobAttributes, 'job-state'),
            'document_numbers' => self::collectValues($payload->documentAttributes, 'document-number'),
            'document_states' => self::collectValues($payload->documentAttributes, 'document-state'),
            'document_formats_supported' => self::collectValues($payload->printerAttributes, 'document-format-supported'),
            'operations_supported' => self::collectValues($payload->printerAttributes, 'operations-supported'),
            'unsupported_attribute_names' => self::groupAttributeNames($payload->unsupportedAttributes),
        ];
    }

    private static function groupCount($groups): int
    {
        if ($groups instanceof \obray\ipp\AttributeGroup) {
            return 1;
        }

        if (!is_array($groups)) {
            return 0;
        }

        return count($groups);
    }

    private static function attributeNames($group): array
    {
        if (!$group instanceof \obray\ipp\AttributeGroup) {
            return [];
        }

        return array_keys($group->jsonSerialize());
    }

    private static function groupAttributeNames($groups): array
    {
        $names = [];
        foreach (self::normalizeGroups($groups) as $group) {
            $names[] = array_keys($group->jsonSerialize());
        }

        return $names;
    }

    private static function collectValues($groups, string $attributeName): array
    {
        $values = [];

        foreach (self::normalizeGroups($groups) as $group) {
            if (!$group->has($attributeName)) {
                continue;
            }

            $attribute = $group->{$attributeName};
            if (!is_array($attribute)) {
                $values[] = (string) $attribute;
                continue;
            }

            foreach ($attribute as $value) {
                $values[] = (string) $value;
            }
        }

        return $values;
    }

    private static function normalizeGroups($groups): array
    {
        if ($groups instanceof \obray\ipp\AttributeGroup) {
            return [$groups];
        }

        if (!is_array($groups)) {
            return [];
        }

        return array_values(array_filter(
            $groups,
            static fn ($group) => $group instanceof \obray\ipp\AttributeGroup
        ));
    }
}
