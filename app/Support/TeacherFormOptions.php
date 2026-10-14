<?php

namespace App\Support;

final class TeacherFormOptions
{
    /**
     * @return list<string>
     */
    public static function prefixes(): array
    {
        return ['อ.', 'ดร.', 'ผศ.', 'ผศ.ดร.', 'รศ.', 'รศ.ดร.', 'ศ.', 'ศ.ดร.'];
    }
}
