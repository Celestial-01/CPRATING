<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class CourseCatalog
{
    public static function all(): Collection
    {
        $courseImages = config('courses.course_images', []);

        return collect(config('courses.catalog', []))->map(function (array $course) use ($courseImages): array {
            $course['image'] = asset($courseImages[$course['code']] ?? $course['image']);
            $course['rating'] = (float) $course['rating'];
            $course['grade_a_chance'] = $course['grades']['A'] ?? 0;
            $course['english_title'] ??= '';
            $course['grade_sample_size'] ??= 0;
            $course['next_courses'] ??= [];

            return $course;
        });
    }

    public static function find(string $code, ?Collection $courses = null): ?array
    {
        return ($courses ?? self::all())->firstWhere('code', strtoupper($code));
    }

    public static function featured(?Collection $courses = null): Collection
    {
        $courses = ($courses ?? self::all())->keyBy('code');

        return collect(config('courses.featured_codes', []))
            ->map(fn (string $code): ?array => $courses->get($code))
            ->filter()
            ->values();
    }

    public static function featuredPopular(?Collection $courses = null, int $limit = 5): Collection
    {
        return self::featured($courses)
            ->sortByDesc('rating')
            ->take($limit)
            ->values();
    }
}
