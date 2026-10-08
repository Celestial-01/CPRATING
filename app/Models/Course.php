<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
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
    ];

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class);
    }

    public function topics(): BelongsToMany
{
    return $this->belongsToMany(Topic::class);
}

public function previousCourses(): BelongsToMany
{
    return $this->belongsToMany(
        Course::class,
        'course_prerequisite',
        'course_id',
        'prerequisite_course_id'
    );
}

public function nextCourses(): BelongsToMany
{
    return $this->belongsToMany(
        Course::class,
        'course_prerequisite',
        'prerequisite_course_id',
        'course_id'
    );
}
}