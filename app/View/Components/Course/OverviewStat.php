<?php

namespace App\View\Components\Course;

use Illuminate\View\Component;
use Illuminate\View\View;

final class OverviewStat extends Component
{
    public string $accentClass;

    public function __construct(public array $stat)
    {
        $this->accentClass = match ($stat['accent'] ?? null) {
            'difficulty' => 'bg-rose-400',
            'workload' => 'bg-[#477ff3]',
            default => 'bg-slate-400',
        };
    }

    public function render(): View
    {
        return view('components.course.overview-stat');
    }
}
