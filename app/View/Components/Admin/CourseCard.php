<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

final class CourseCard extends Component
{
    public string $imageClasses;

    public function __construct(public mixed $course)
    {
        $this->imageClasses = 'absolute inset-0 size-full object-cover transition duration-300 group-hover:scale-105'.($course->is_offered ? ' brightness-[0.8]' : ' brightness-[0.55]');
    }

    public function render(): View
    {
        return view('components.admin.course-card');
    }
}
