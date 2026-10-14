<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRating extends Model
{
    public const GRADE_POINTS = [
        'A' => 4.0,
        'B+' => 3.5,
        'B' => 3.0,
        'C+' => 2.5,
        'C' => 2.0,
        'D+' => 1.5,
        'D' => 1.0,
        'F' => 0.0,
    ];

    protected $fillable = [
        'user_id',
        'course_code',
        'difficulty',
        'workload',
        'interest',
        'grade',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_code', 'course_code');
    }

    protected function casts(): array
    {
        return [
            'difficulty' => 'integer',
            'workload' => 'integer',
            'interest' => 'integer',
        ];
    }
}
