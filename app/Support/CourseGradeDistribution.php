<?php

namespace App\Support;

final class CourseGradeDistribution
{
    public static function forCourse(array $course): array
    {
        return collect($course['grades'] ?? [])
            ->map(
                static fn (int|float $percentage, string $grade): array => [
                    'grade' => $grade,
                    'percentage' => $percentage,
                    'barHeight' => max(18, $percentage * 1.8),
                    'highlighted' => $grade === 'A',
                ],
            )
            ->values()
            ->all();
    }
}
