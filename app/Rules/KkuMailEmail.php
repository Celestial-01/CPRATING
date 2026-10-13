<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class KkuMailEmail implements ValidationRule
{
    public static function accepts(mixed $email): bool
    {
        if (! is_string($email)) {
            return false;
        }

        $parts = explode('@', $email);

        return count($parts) === 2
            && $parts[0] !== ''
            && strcasecmp($parts[1], 'kkumail.com') === 0;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::accepts($value)) {
            $fail('กรุณาใช้อีเมลมหาวิทยาลัยที่ลงท้ายด้วย @kkumail.com');
        }
    }
}
