<?php

namespace App\Services;

use App\Models\BannedWord;
use Illuminate\Support\Facades\Cache;

class ContentModerator
{
    public static function normalize(string $text): string
    {
        $text = mb_strtolower($text);

        return preg_replace('/[^\p{L}\p{N}\p{M}]+/u', '', $text) ?? '';
    }

    public static function containsBlocked(string $text): bool
    {
        return self::matches($text, 'block');
    }

    public static function needsReview(string $text): bool
    {
        return self::matches($text, 'review');
    }

    private static function matches(string $text, string $type): bool
    {
        $normalized = self::normalize($text);

        $words = Cache::remember("banned_words_{$type}", 3600, function () use ($type) {
            return BannedWord::where('type', $type)
                ->pluck('word')
                ->map(fn ($w) => self::normalize($w))
                ->filter()
                ->values()
                ->all();
        });

        foreach ($words as $word) {
            if (str_contains($normalized, $word)) {
                return true;
            }
        }

        return false;
    }
    public function __construct()
    {
        //
    }
}
