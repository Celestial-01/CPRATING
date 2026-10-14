<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class StudyPlan extends Model
{
    protected $fillable = [
        'major_code',
        'name',
        'image',
        'academic_year_start',
        'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function scopeForPublicCatalog(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereHas('courses.course', fn ($course) => $course->published())
            ->with([
                'courses' => fn ($entries) => $entries
                    ->whereHas('course', fn ($course) => $course->published())
                    ->with(['course.prerequisiteCourses' => fn ($course) => $course->published()]),
            ]);
    }

    public static function latestPublishedPerMajor(): Collection
    {
        return self::query()
            ->forPublicCatalog()
            ->orderByDesc('academic_year_start')
            ->get()
            ->unique('major_code')
            ->values();
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function termCredits(int $year, int $term): int
    {
        return (int) $this->courses
            ->filter(fn (StudyPlanCourse $entry): bool => $entry->year === $year && $entry->term === $term)
            ->sum(fn (StudyPlanCourse $entry): int => $entry->course->credits);
    }

    public function accumulatedCredits(int $year, int $term): int
    {
        return (int) $this->courses
            ->filter(fn (StudyPlanCourse $entry): bool => $entry->year < $year
                || ($entry->year === $year && $entry->term <= $term))
            ->sum(fn (StudyPlanCourse $entry): int => $entry->course->credits);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(StudyPlanCourse::class)->orderBy('year')->orderBy('term')->orderBy('position');
    }
}
