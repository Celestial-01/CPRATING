<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Administrative catalog choices supplied by the existing admin interface.
     * Stored course values remain the source of truth when they differ.
     *
     * @var array<string, string>
     */
    public const MAJORS = [
        'CS' => 'Computer Science',
        'ITII' => 'Information Technology and Innovation Intelligence',
        'AI' => 'Artificial Intelligence',
        'CY' => 'Cybersecurity',
        'GIS' => 'Geo-informatics',
    ];

    /**
     * @var list<string>
     */
    public const TYPES = [
        'วิชาบังคับ',
        'วิชาเลือกเฉพาะทาง',
    ];

    public const PUBLICATION_DRAFT = 'draft';

    public const PUBLICATION_PUBLISHED = 'published';

    public const PUBLICATION_HIDDEN = 'hidden';

    public const PUBLICATION_STATUSES = [
        self::PUBLICATION_DRAFT,
        self::PUBLICATION_PUBLISHED,
        self::PUBLICATION_HIDDEN,
    ];

    public const PUBLICATION_STATUS_LABELS = [
        self::PUBLICATION_DRAFT => 'แบบร่าง',
        self::PUBLICATION_PUBLISHED => 'เผยแพร่แล้ว',
        self::PUBLICATION_HIDDEN => 'ซ่อนอยู่',
    ];

    protected $fillable = [
        'course_name_en',
        'course_name_th',
        'course_code',
        'description',
        'credits',
        'major',
        'course_type',
        'image',
        'is_offered',
        'publication_status',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'is_offered' => 'boolean',
            'views' => 'integer',
        ];
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => self::normalizeImagePath($value),
        );
    }

    public static function normalizeImagePath(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $parsed = parse_url($value);

        if ($parsed !== false && isset($parsed['scheme'])) {
            $path = ltrim((string) ($parsed['path'] ?? ''), '/');

            return str_starts_with($path, 'storage/')
                ? substr($path, strlen('storage/'))
                : $value;
        }

        $path = ltrim($value, '/');

        return str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publication_status', self::PUBLICATION_PUBLISHED);
    }

    public function scopeOffered(Builder $query): Builder
    {
        return $query->where('is_offered', true);
    }

    public function scopeForPublicCatalog(Builder $query): Builder
    {
        return $query
            ->published()
            ->with([
                'teachers',
                'topics',
                'prerequisiteCourses' => fn ($query) => $query->published(),
                'dependentCourses' => fn ($query) => $query->published(),
            ])
            ->withStatistics();
    }

    public function scopeWithStatistics(Builder $query): Builder
    {
        return $query
            ->withCount([
                'comments' => fn ($query) => $query->approved(),
                'ratings',
            ])
            ->withAvg('ratings as interest_average', 'interest')
            ->withAvg('ratings as difficulty_average', 'difficulty')
            ->withAvg('ratings as workload_average', 'workload');
    }

    public function scopeMatching(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $pattern = '%'.$search.'%';

            $query->where('course_code', 'like', $pattern)
                ->orWhere('course_name_th', 'like', $pattern)
                ->orWhere('course_name_en', 'like', $pattern);
        });
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('views');
    }

    public function recordView(): void
    {
        static::withoutTimestamps(fn () => $this->increment('views'));
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'course_code', 'course_code');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(CourseRating::class, 'course_code', 'course_code');
    }

    public function studyPlanCourses(): HasMany
    {
        return $this->hasMany(StudyPlanCourse::class);
    }

    public function updateDetails(array $attributes): void
    {
        $this->getConnection()->transaction(function () use ($attributes): void {
            $courseCode = $attributes['course_code'] ?? $this->course_code;

            if ($courseCode !== $this->course_code) {
                $this->comments()->update(['course_code' => $courseCode]);
                $this->ratings()->update(['course_code' => $courseCode]);
            }

            $this->update($attributes);
        });
    }

    /** @return Collection<string, int> */
    public function reportedGradeCounts(): Collection
    {
        $gradeCounts = $this->ratings()
            ->whereIn('grade', array_keys(CourseRating::GRADE_POINTS))
            ->select('grade')
            ->selectRaw('COUNT(*) as grade_count')
            ->groupBy('grade')
            ->pluck('grade_count', 'grade');

        return collect(CourseRating::GRADE_POINTS)
            ->map(fn (float $gradePoint, string $grade): int => (int) $gradeCounts->get($grade, 0));
    }

    /** @param Collection<string, int> $gradeCounts */
    public function averageGradePoint(Collection $gradeCounts): ?float
    {
        $sampleSize = $gradeCounts->sum();

        if ($sampleSize === 0) {
            return null;
        }

        $totalGradePoints = $gradeCounts
            ->map(fn (int $count, string $grade): float => CourseRating::GRADE_POINTS[$grade] * $count)
            ->sum();

        return round($totalGradePoints / $sampleSize, 2);
    }

    /** @param Collection<string, int> $gradeCounts */
    public function gradePercentage(string $grade, Collection $gradeCounts): ?float
    {
        $sampleSize = $gradeCounts->sum();

        if ($sampleSize === 0) {
            return null;
        }

        return round($gradeCounts->get($grade, 0) / $sampleSize * 100, 1);
    }

    public function imageUrl(): ?string
    {
        $image = self::normalizeImagePath($this->image);

        if (! $image) {
            return null;
        }

        return filter_var($image, FILTER_VALIDATE_URL)
            ? $image
            : Storage::disk('public')->url($image);
    }

    public function imageStoragePath(): ?string
    {
        $image = self::normalizeImagePath($this->image);

        return $image && ! filter_var($image, FILTER_VALIDATE_URL)
            ? $image
            : null;
    }

    public function interestRating(): ?float
    {
        return $this->interest_average === null
            ? null
            : round((float) $this->interest_average, 1);
    }

    public function difficultyPercentage(): ?int
    {
        return $this->difficulty_average === null
            ? null
            : (int) round((float) $this->difficulty_average * 20);
    }

    public function workloadPercentage(): ?int
    {
        return $this->workload_average === null
            ? null
            : (int) round((float) $this->workload_average * 20);
    }

    public function instructorNames(): string
    {
        return $this->teachers
            ->map(fn (Teacher $teacher): string => $teacher->display_name)
            ->implode(', ');
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class);
    }

    public function prerequisiteCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisite',
            'course_id',
            'prerequisite_course_id',
        );
    }

    public function dependentCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisite',
            'prerequisite_course_id',
            'course_id',
        );
    }
}
