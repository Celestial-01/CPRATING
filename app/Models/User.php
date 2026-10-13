<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'email_verified_at', 'role', 'student_id', 'major_code'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function googleAccount(): HasOne
    {
        return $this->hasOne(SocialAccount::class)
            ->where('provider', 'google');
    }

    public function displayName(): string
    {
        return $this->name ?: Str::before($this->email, '@');
    }

    public function firstName(): string
    {
        return Str::of($this->displayName())
            ->squish()
            ->before(' ')
            ->toString();
    }

    public function hasCompletedProfile(): bool
    {
        return filled($this->student_id) && filled($this->major_code);
    }

    public function cohortTag(): ?string
    {
        if (! is_string($this->student_id)
            || ! preg_match('/^(\d{2})\d{7}-\d$/', $this->student_id, $matches)) {
            return null;
        }

        $cohort = (int) $matches[1] - 6;

        return $cohort >= 0 ? 'kku'.$cohort : null;
    }

    /**
     * @return list<string>
     */
    public function profileTags(): array
    {
        return array_values(array_filter([
            $this->cohortTag(),
            $this->major_code,
        ]));
    }

    public function avatarUrl(): ?string
    {
        return $this->googleAccount?->avatar_url;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
