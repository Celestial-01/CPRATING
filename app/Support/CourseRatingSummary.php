<?php

namespace App\Support;

use App\Models\CourseRating;

final class CourseRatingSummary
{
    public static function forCourse(string $courseCode): array
    {
        $average = CourseRating::query()
            ->where('course_code', $courseCode)
            ->selectRaw('COUNT(*) as rating_count, AVG(difficulty) as difficulty, AVG(workload) as workload, AVG(interest) as interest')
            ->first();

        $count = (int) $average->rating_count;

        return [
            'count' => $count,
            'rating' => $count > 0 ? round((float) $average->interest, 1) : null,
            'difficulty' => $count > 0 ? (int) round((float) $average->difficulty * 20) : null,
            'workload' => $count > 0 ? (int) round((float) $average->workload * 20) : null,
        ];
    }
}
