<?php

namespace App\Support;

final class CourseCommentOptions
{
    /**
     * @return list<array{label: string, active: bool}>
     */
    public static function sortOptions(): array
    {
        return [
            ['label' => 'ล่าสุด', 'active' => true],
            ['label' => 'ยอดนิยม', 'active' => false],
        ];
    }
}
