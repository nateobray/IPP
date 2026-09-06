<?php
namespace obray\ipp\enums;

class JobState extends \obray\ipp\types\Enum
{
    const PENDING = 3;
    const PENDINGHELD = 4;
    const PROCESSING = 5;
    const PROCESSINGSTOPPED = 6;
    const CANCELED = 7;
    const ABORTED = 8;
    const COMPLETED = 9;

    public function __toString(): string
    {
        // Preserve the existing public constant names while displaying the
        // standard RFC 8011 §5.3.7 names for these two compound states.
        return match ((int) $this->value) {
            self::PENDINGHELD => 'pending-held',
            self::PROCESSINGSTOPPED => 'processing-stopped',
            default => parent::__toString(),
        };
    }

    public function jsonSerialize(): mixed
    {
        return (string) $this;
    }
}
