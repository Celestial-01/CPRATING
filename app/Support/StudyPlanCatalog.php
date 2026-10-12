<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class StudyPlanCatalog
{
    public static function majors(): Collection
    {
        $majorImages = config('study-plans.major_images', []);

        return collect(config('study-plans.majors', []))->map(function (array $major) use ($majorImages): array {
            $major['image'] = asset($majorImages[$major['key']] ?? $major['image']);

            return $major;
        });
    }

    public static function yearOptions(): array
    {
        return array_values(config('study-plans.years', []));
    }

    public static function findMajor(string $key, ?Collection $majors = null): ?array
    {
        return ($majors ?? self::majors())->firstWhere('key', $key);
    }

    public static function years(string $majorKey, ?Collection $majors = null): array
    {
        $major = self::findMajor($majorKey, $majors);

        if ($major === null) {
            return [];
        }

        $firstYear = config('study-plans.first_year', []);
        $elective = config('study-plans.electives.0');
        $freeElective = config('study-plans.electives.1');

        $majorCourses = collect($major['courses'])
            ->map(fn (array $course): array => [
                'code' => $course['code'],
                'name' => $course['name'],
                'english_name' => '',
                'credits' => '3(3-0-6)',
                'prerequisite' => 'ไม่มี',
            ])
            ->values()
            ->all();

        return [
            1 => $firstYear,
            2 => [
                [
                    $majorCourses[0] ?? self::placeholder(),
                    $majorCourses[1] ?? self::placeholder(),
                    $majorCourses[2] ?? self::placeholder(),
                    self::withCode($elective),
                    self::withoutCode($freeElective),
                ],
                [
                    $majorCourses[3] ?? self::placeholder(),
                    $majorCourses[4] ?? self::placeholder(),
                    $majorCourses[5] ?? self::placeholder(),
                    self::withCode(config('study-plans.electives.2')),
                    self::withoutCode($freeElective),
                ],
            ],
            3 => [
                [
                    self::withCode($elective),
                    self::withCode($elective),
                    self::withCode($elective),
                    self::withoutCode($freeElective),
                ],
                [
                    self::project('CP 499 001', 'โครงงานพิเศษ 1', 'Special Project I', '3(1-4-4)', 'ไม่มี'),
                    self::withCode($elective),
                    self::withCode($elective),
                    self::withoutCode($freeElective),
                ],
            ],
            4 => [
                [
                    self::project('CP 499 002', 'โครงงานพิเศษ 2', 'Special Project II', '3(1-4-4)', 'CP 499 001'),
                    self::withCode($elective),
                    self::withCode($elective),
                    self::withoutCode($freeElective),
                ],
                [self::project('CP 499 101', 'สหกิจศึกษา', 'Cooperative Education', '6', 'ไม่มี')],
            ],
        ];
    }

    private static function placeholder(): array
    {
        return [
            'code' => 'CP 3XX XXX',
            'name' => 'วิชาเลือกเฉพาะทาง',
            'english_name' => 'Specialized Electives',
            'credits' => '3',
            'prerequisite' => '-',
        ];
    }

    private static function withCode(?array $course): array
    {
        return [
            'code' => 'CP 3XX XXX',
            'name' => $course['name'] ?? 'วิชาเลือก',
            'english_name' => $course['english_name'] ?? '',
            'credits' => $course['credits'] ?? '3',
            'prerequisite' => $course['prerequisite'] ?? '-',
        ];
    }

    private static function withoutCode(?array $course): array
    {
        return [
            'code' => '—',
            'name' => $course['name'] ?? 'วิชาเลือกเสรี',
            'english_name' => $course['english_name'] ?? '',
            'credits' => $course['credits'] ?? '3',
            'prerequisite' => $course['prerequisite'] ?? '-',
        ];
    }

    private static function project(string $code, string $name, string $englishName, string $credits, string $prerequisite): array
    {
        return [
            'code' => $code,
            'name' => $name,
            'english_name' => $englishName,
            'credits' => $credits,
            'prerequisite' => $prerequisite,
        ];
    }
}
