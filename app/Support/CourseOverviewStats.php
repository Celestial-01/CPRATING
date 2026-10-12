<?php

namespace App\Support;

final class CourseOverviewStats
{
    private const HEAVY_THRESHOLD = 65;

    public static function forCourse(array $course): array
    {
        return [
            self::stat(
                title: 'ระดับความยาก',
                percentage: $course['difficulty'],
                heavyLabel: 'ยาก',
                accent: 'difficulty',
                ratingCount: $course['rating_count'],
            ),
            self::stat(
                title: 'ปริมาณงาน',
                percentage: $course['workload'],
                heavyLabel: 'ค่อนข้างหนัก',
                accent: 'workload',
                ratingCount: $course['rating_count'],
            ),
        ];
    }

    private static function stat(
        string $title,
        ?int $percentage,
        string $heavyLabel,
        string $accent,
        int $ratingCount,
    ): array {
        return [
            'title' => $title,
            'percentage' => $percentage,
            'summary' => match (true) {
                $percentage === null => 'ยังไม่มีคะแนน',
                $percentage >= self::HEAVY_THRESHOLD => $heavyLabel,
                $percentage >= 40 => 'ปานกลาง',
                default => 'น้อย',
            },
            'description' => $ratingCount > 0
                ? 'เฉลี่ยจากผู้ให้คะแนน '.number_format($ratingCount).' คน'
                : 'ยังไม่มีผู้ให้คะแนน',
            'accent' => $accent,
        ];
    }
}
