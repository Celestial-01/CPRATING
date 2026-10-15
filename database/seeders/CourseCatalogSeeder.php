<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class CourseCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = $this->courseDefinitions();
        $coursesByCode = [];

        foreach ($definitions as $definition) {
            $course = Course::query()->updateOrCreate(
                ['course_code' => $definition['course_code']],
                $definition['attributes'],
            );

            $coursesByCode[$course->course_code] = $course;
        }

        foreach ($definitions as $definition) {
            $course = $coursesByCode[$definition['course_code']] ?? null;

            if ($course === null) {
                continue;
            }

            $prerequisiteIds = collect($definition['prerequisites'])
                ->map(fn (string $code): ?int => $coursesByCode[$code]?->getKey())
                ->filter()
                ->values()
                ->all();

            $course->prerequisiteCourses()->sync($prerequisiteIds);
        }
    }

    /**
     * Build seed records from the curriculum source used by the study-plan page.
     * Placeholder and general-education rows are intentionally excluded because
     * they do not represent catalog records managed by the admin interface.
     *
     * @return Collection<int, array{course_code: string, attributes: array<string, mixed>, prerequisites: list<string>}>
     */
    private function courseDefinitions(): Collection
    {
        $curriculum = File::json(resource_path('data/study-plans.json'));
        $records = [];

        $collect = function (mixed $value) use (&$collect, &$records): void {
            if (! is_array($value)) {
                return;
            }

            if (array_key_exists('code', $value)) {
                $courseCode = $this->normalizeCode($value['code']);

                if ($courseCode === null) {
                    return;
                }

                $records[$courseCode] = [
                    'course_code' => $courseCode,
                    'attributes' => $this->courseAttributes($courseCode, $value),
                    'prerequisites' => $this->extractPrerequisites($value['prerequisite'] ?? null),
                ];

                return;
            }

            foreach ($value as $child) {
                $collect($child);
            }
        };

        $collect($curriculum['common_years'] ?? []);

        foreach ($curriculum['majors'] ?? [] as $major) {
            $collect($major['second_year'] ?? []);
        }

        return collect($records)->sortBy('course_code')->values();
    }

    /** @param array<string, mixed> $definition */
    private function courseAttributes(string $courseCode, array $definition): array
    {
        $thaiName = trim((string) ($definition['name'] ?? ''));
        $englishName = trim((string) ($definition['english_name'] ?? ''));

        return [
            'course_name_en' => $englishName !== '' ? $englishName : $courseCode,
            'course_name_th' => $thaiName !== '' ? $thaiName : $courseCode,
            'description' => null,
            'credits' => $this->creditValue($definition['credits'] ?? null),
            'major' => $this->majorFor($courseCode),
            'course_type' => Course::TYPES[0],
            'image' => null,
            'is_offered' => true,
            'publication_status' => Course::PUBLICATION_PUBLISHED,
        ];
    }

    private function normalizeCode(mixed $value): ?string
    {
        $code = strtoupper((string) preg_replace('/\s+/', '', trim((string) $value)));

        return preg_match('/^[A-Z]{2}\d{6}$/', $code) === 1 ? $code : null;
    }

    /** @return list<string> */
    private function extractPrerequisites(mixed $value): array
    {
        preg_match_all('/[A-Z]{2}\s*\d{6}/i', (string) $value, $matches);

        return collect($matches[0] ?? [])
            ->map(fn (string $code): ?string => $this->normalizeCode($code))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function creditValue(mixed $value): int
    {
        preg_match('/\d+/', (string) $value, $matches);

        return max(1, (int) ($matches[0] ?? 3));
    }

    private function majorFor(string $courseCode): string
    {
        return match (true) {
            str_starts_with($courseCode, 'CP410') => 'ITII',
            str_starts_with($courseCode, 'CP422') => 'CY',
            str_starts_with($courseCode, 'CP354') => 'AI',
            str_starts_with($courseCode, 'CP352') => 'CS',
            str_starts_with($courseCode, 'GI') => 'GIS',
            default => 'CS',
        };
    }
}
