<?php

namespace App\View\Components\Course;

use Illuminate\View\Component;
use Illuminate\View\View;

final class Card extends Component
{
    public string $classes;

    public string $searchTerm;

    public function __construct(
        public array $course,
        public bool $compact = false,
        public bool $saveable = true,
        public bool $history = false,
    ) {
        $this->classes = trim('cc'.($compact ? ' cc--compact' : ''));
        $this->searchTerm = mb_strtolower(implode(' ', [
            $course['code'],
            $course['title'],
            $course['category'],
            $course['major_code'],
        ]));
    }

    public function render(): View
    {
        return view('components.course.card');
    }
}
