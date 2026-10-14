<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    /** @var list<string> */
    public const PREFIXES = ['อ.', 'ดร.', 'ผศ.', 'ผศ.ดร.', 'รศ.', 'รศ.ดร.', 'ศ.', 'ศ.ดร.'];

    protected $fillable = [
        'prefix',
        'teacher_name',
        'image',
    ];

    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => trim($this->prefix.' '.$this->teacher_name));
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }
}
