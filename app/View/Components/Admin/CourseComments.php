<?php

namespace App\View\Components\Admin;

use App\Support\CourseCommentOptions;
use Illuminate\View\Component;
use Illuminate\View\View;

final class CourseComments extends Component
{
    public array $sortOptions;

    public function __construct()
    {
        $this->sortOptions = CourseCommentOptions::sortOptions();
    }

    public function render(): View
    {
        return view('components.admin.course-comments');
    }
}
