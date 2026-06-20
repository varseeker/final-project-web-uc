<?php

namespace App\Support;

class CustomerPhone
{
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    public static function display(?string $phone): string
    {
        $normalized = self::normalize($phone);

        if ($normalized === null) {
            return (string) $phone;
        }

        if (str_starts_with($normalized, '62')) {
            return '0'.substr($normalized, 2);
        }

        return $normalized;
    }
}
