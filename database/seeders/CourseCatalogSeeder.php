<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class CourseCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $courseDefinitions = collect(config('courses.catalog', []));

        $courseDefinitions->each(function (array $courseDefinition): void {
            $course = Course::query()->updateOrCreate(
                ['course_code' => $courseDefinition['code']],
                $this->courseAttributes($courseDefinition),
            );

            $this->syncTeacher($course, $courseDefinition['instructor'] ?? null);
            $this->syncTopics($course, $courseDefinition['topics'] ?? []);
        });

        $courseIdsByCode = Course::query()->pluck('id', 'course_code');

        $courseDefinitions->each(function (array $courseDefinition) use ($courseIdsByCode): void {
            $course = Course::query()
                ->where('course_code', $courseDefinition['code'])
                ->first();

            if ($course === null) {
                return;
            }

            $prerequisiteIds = collect($courseDefinition['prerequisites'] ?? [])
                ->pluck('code')
                ->map(fn (string $courseCode): ?int => $courseIdsByCode->get($courseCode))
                ->filter()
                ->all();

            $course->prerequisiteCourses()->sync($prerequisiteIds);
        });
    }

    private function courseAttributes(array $courseDefinition): array
    {
        return [
            'course_name_en' => $courseDefinition['english_title'] ?? $courseDefinition['title'],
            'course_name_th' => $courseDefinition['title'],
            'description' => $courseDefinition['description'] ?? null,
            'credits' => $this->creditValue($courseDefinition['credits'] ?? null),
            'major' => 'วิทยาลัยการคอมพิวเตอร์',
            'course_type' => $courseDefinition['category'] ?? 'วิชาเฉพาะ',
            'image' => $courseDefinition['image'] ?? null,
            'is_offered' => true,
            'publication_status' => Course::PUBLICATION_PUBLISHED,
        ];
    }

    private function syncTeacher(Course $course, ?string $instructor): void
    {
        $instructor = trim((string) $instructor);

        if ($instructor === '') {
            $course->teachers()->detach();

            return;
        }

        [$prefix, $teacherName] = $this->splitTeacherName($instructor);
        $teacher = Teacher::query()->updateOrCreate(
            ['teacher_name' => $teacherName],
            ['prefix' => $prefix],
        );

        $course->teachers()->sync([$teacher->id]);
    }

    private function syncTopics(Course $course, array $topicNames): void
    {
        $topicIds = collect($topicNames)
            ->filter(fn (mixed $topicName): bool => is_string($topicName) && trim($topicName) !== '')
            ->map(fn (string $topicName): int => Topic::query()
                ->firstOrCreate(['name' => trim($topicName)])
                ->getKey())
            ->all();

        $course->topics()->sync($topicIds);
    }

    private function creditValue(mixed $credits): int
    {
        preg_match('/\d+/', (string) $credits, $matches);

        return (int) ($matches[0] ?? 0);
    }

    private function splitTeacherName(string $instructor): array
    {
        foreach (['อ. ดร.', 'อ.'] as $prefix) {
            if (str_starts_with($instructor, $prefix)) {
                return [$prefix, trim(substr($instructor, mb_strlen($prefix)))];
            }
        }

        return [null, $instructor];
    }
}
