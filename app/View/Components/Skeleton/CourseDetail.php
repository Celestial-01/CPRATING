<?php

namespace App\View\Components\Skeleton;

use Illuminate\View\Component;
use Illuminate\View\View;

final class CourseDetail extends Component
{
    public string $pulse = 'animate-pulse bg-slate-200 motion-reduce:animate-none';

    public string $softPulse = 'animate-pulse bg-slate-100 motion-reduce:animate-none';

    public function render(): View
    {
        return view('components.skeleton.course-detail');
    }
}
