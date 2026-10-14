<?php

namespace App\Rules;

use App\Services\ContentModerator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NoBannedWords implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (ContentModerator::containsBlocked((string) $value)) {
            $fail('ข้อความมีคำที่ไม่เหมาะสม ไม่สามารถโพสต์ได้ กรุณาลองใหม่อีกครั้ง');
        }
    }
}
