<?php

namespace obray\ipp\transport;

final class DecodeGuard
{
    public static function requireBytes(string $binary, int $offset, int $length, string $context): void
    {
        if ($offset < 0) {
            throw new \UnexpectedValueException(sprintf('Invalid negative offset %d while decoding %s.', $offset, $context));
        }

        if ($length < 0) {
            throw new \UnexpectedValueException(sprintf('Invalid negative length %d while decoding %s.', $length, $context));
        }

        if (strlen($binary) < ($offset + $length)) {
            throw new \UnexpectedValueException(sprintf(
                'Truncated IPP payload while decoding %s at offset %d.',
                $context,
                $offset
            ));
        }
    }

    public static function unpack(string $format, string $binary, int $offset, int $length, string $context): array
    {
        self::requireBytes($binary, $offset, $length, $context);

        $unpacked = unpack($format, $binary, $offset);
        if ($unpacked === false) {
            throw new \UnexpectedValueException(sprintf(
                'Failed to unpack %s at offset %d.',
                $context,
                $offset
            ));
        }

        return $unpacked;
    }

    public static function readByte(string $binary, int $offset, string $context): int
    {
        return self::unpack('cvalue', $binary, $offset, 1, $context)['value'];
    }
}
