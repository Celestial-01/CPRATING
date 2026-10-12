<?php

namespace App\Models;

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
}
