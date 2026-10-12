<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefix',
        'teacher_name',
        'image',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }
}
