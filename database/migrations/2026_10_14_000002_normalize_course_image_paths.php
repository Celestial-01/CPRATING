<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('courses')
            ->whereNotNull('image')
            ->orderBy('id')
            ->get(['id', 'image'])
            ->each(function (object $course): void {
                $imagePath = $this->normalizeImagePath($course->image);

                if ($imagePath !== $course->image) {
                    DB::table('courses')
                        ->where('id', $course->id)
                        ->update(['image' => $imagePath]);
                }
            });
    }

    public function down(): void
    {
        // The migration removes environment-specific hostnames and is not reversible.
    }

    private function normalizeImagePath(?string $value): ?string
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
};
