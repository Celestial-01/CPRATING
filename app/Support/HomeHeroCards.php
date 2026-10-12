<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class HomeHeroCards
{
    public static function forCourses(Collection $courses): Collection
    {
        $placements = collect(config('home.hero_float_cards', []));
        $fallback = $placements->first() ?? ['variant' => 'left-top'];

        return $courses->values()->map(
            function (array $course, int $index) use ($placements, $fallback): array {
                $placement = $placements->get($index, $fallback);

                return [
                    'course' => $course,
                    'cssClass' => self::cssClass($placement, $fallback),
                ];
            },
        );
    }

    private static function cssClass(array $placement, array $fallback): string
    {
        $variant = $placement['variant'] ?? $fallback['variant'];
        $classes = ['mc', "mc--{$variant}"];

        if (($placement['size'] ?? 'default') === 'small') {
            $classes[] = 'mc--small';
        }

        if ($placement['far'] ?? false) {
            $classes[] = 'mc--far';
        }

        return implode(' ', $classes);
    }
}
