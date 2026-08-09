<?php

namespace App\Support;

use InvalidArgumentException;

class PhoneNumber
{
    public static function normalize(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', trim($phone));

        if (preg_match('/^07\d{8}$/', $phone)) {
            return '254' . substr($phone, 1);
        }

        if (preg_match('/^01\d{8}$/', $phone)) {
            return '254' . substr($phone, 1);
        }

        if (preg_match('/^254[17]\d{8}$/', $phone)) {
            return $phone;
        }

        if (preg_match('/^\+254[17]\d{8}$/', $phone)) {
            return substr($phone, 1);
        }

        throw new InvalidArgumentException(
            'Invalid Kenyan mobile number.'
        );
    }
}
