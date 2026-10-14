<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

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
        'is_active',
        'is_offered',
        'publication_status',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'is_offered' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publication_status', self::PUBLICATION_PUBLISHED);
    }

    public function scopeOffered(Builder $query): Builder
    {
        return $query->where('is_offered', true);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class);
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

    /**
     * Compatibility aliases for the admin controller and views from main.
     */
    public function previousCourses(): BelongsToMany
    {
        return $this->prerequisiteCourses();
    }

    public function nextCourses(): BelongsToMany
    {
        return $this->dependentCourses();
    }

    protected function getIsActiveAttribute(): bool
    {
        return (bool) ($this->attributes['is_offered'] ?? false);
    }

    protected function setIsActiveAttribute(mixed $value): void
    {
        $this->attributes['is_offered'] = (bool) $value;
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
}
