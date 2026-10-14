<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPlanCourse extends Model
{
    protected $fillable = [
        'study_plan_id',
        'course_id',
        'year',
        'term',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'term' => 'integer',
            'position' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }
}
