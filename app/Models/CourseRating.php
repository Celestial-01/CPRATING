<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRating extends Model
{
    protected $fillable = [
        'user_id',
        'course_code',
        'difficulty',
        'workload',
        'interest',
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
