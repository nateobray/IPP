<?php

namespace obray\ipp;

/**
 * Document Attributes (PWG5100.5 — Document Object)
 *
 * Attribute group tag 0x09. Carries document description and document
 * template attributes returned in Get-Document-Attributes and
 * Get-Documents responses, and sent in Create-Document /
 * Set-Document-Attributes requests.
 */
class DocumentAttributes extends \obray\ipp\AttributeGroup
{
    protected $attribute_group_tag = 0x09;

    public function set(string $name, $value): void
    {
        switch ($name) {

            // ── Document description: identification ─────────────────────
            case 'document-number':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'document-name':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::NAME, 255);
                break;
            case 'document-uri':
            case 'document-uuid':
            case 'more-info':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::URI, 1023);
                break;
            case 'document-format':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::MIMEMEDIATYPE);
                break;
            case 'document-format-version':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::TEXT, 127);
                break;
            case 'document-natural-language':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::NATURALLANGUAGE);
                break;
            case 'document-charset':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::CHARSET);
                break;
            case 'document-message':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::TEXT);
                break;

            // ── Document description: state ──────────────────────────────
            case 'document-state':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'document-state-reasons':
                $this->attributes[$name] = $this->createAttributeInstances($name, (array) $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'document-state-message':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::TEXT);
                break;

            // ── Document description: progress / accounting ───────────────
            case 'impressions':
            case 'impressions-completed':
            case 'k-octets':
            case 'k-octets-processed':
            case 'media-sheets':
            case 'media-sheets-completed':
            case 'time-at-creation':
            case 'time-at-processing':
            case 'time-at-completed':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'date-time-at-creation':
            case 'date-time-at-processing':
            case 'date-time-at-completed':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::DATETIME);
                break;

            // ── Document description: misc ───────────────────────────────
            case 'last-document':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::BOOLEAN);
                break;
            case 'output-device-assigned':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::NAME, 127);
                break;

            // ── Document template attributes ─────────────────────────────
            case 'copies':
            case 'number-up':
            case 'x-image-shift':
            case 'x-side1-image-shift':
            case 'x-side2-image-shift':
            case 'y-image-shift':
            case 'y-side1-image-shift':
            case 'y-side2-image-shift':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                break;
            case 'orientation-requested':
            case 'print-quality':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'finishings':
                $this->attributes[$name] = $this->createAttributeInstances($name, (array) $value, \obray\ipp\enums\Types::ENUM);
                break;
            case 'page-ranges':
                $this->attributes[$name] = $this->createAttributeInstances($name, (array) $value, \obray\ipp\enums\Types::RANGEOFINTEGER);
                break;
            case 'sides':
            case 'sheet-collate':
            case 'output-bin':
            case 'x-image-position':
            case 'y-image-position':
            case 'media':
            case 'media-input-tray-check':
            case 'imposition-template':
            case 'page-delivery':
            case 'page-order-received':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::KEYWORD);
                break;
            case 'printer-resolution':
                $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::RESOLUTION);
                break;
            case 'finishings-col':
            case 'media-col':
            case 'separator-sheets':
            case 'cover-back':
            case 'cover-front':
            case 'insert-sheet':
            case 'document-format-details':
                $this->attributes[$name] = new \obray\ipp\CollectionAttribute($name, $value);
                break;
            case 'overrides':
            case 'force-front-side':
                $this->attributes[$name] = $this->createAttributeInstances($name, (array) $value, \obray\ipp\enums\Types::COLLECTION);
                break;

            default:
                if (is_bool($value)) {
                    $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::BOOLEAN);
                } elseif (is_int($value)) {
                    $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::INTEGER);
                } elseif (is_string($value)) {
                    $this->attributes[$name] = new \obray\ipp\Attribute($name, $value, \obray\ipp\enums\Types::KEYWORD);
                } else {
                    throw new \Exception("Unsupported document attribute: '$name'");
                }
        }
    }
}
