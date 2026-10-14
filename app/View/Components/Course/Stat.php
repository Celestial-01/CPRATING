<?php

namespace App\View\Components\Course;

use Illuminate\View\Component;
use Illuminate\View\View;

final class Stat extends Component
{
    public string $valueClasses;

    public function __construct(
        public mixed $value,
        public string $label,
        public string $tone = 'default',
    ) {
        $this->valueClasses = 'order-1 text-base font-bold leading-5 '.match ($tone) {
            'rating' => 'text-[#ffa51f]',
            'views' => 'text-[#0189ff]',
            default => 'text-black',
        };
    }

    public function render(): View
    {
        return view('components.course.stat');
    }
}
