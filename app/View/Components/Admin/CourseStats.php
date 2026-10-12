<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

final class CourseStats extends Component
{
    /**
     * @var list<array{title: string, label: string}>
     */
    public array $metrics;

    /**
     * @var list<string>
     */
    public array $grades;

    public function __construct(
        array $metrics = [],
        array $grades = [],
    ) {
        $this->metrics = $metrics !== [] ? $metrics : [
            ['title' => 'ความยากของวิชา', 'label' => 'คะแนนเฉลี่ย'],
            ['title' => 'ปริมาณงานและการบ้าน', 'label' => 'คะแนนเฉลี่ย'],
        ];
        $this->grades = $grades !== [] ? $grades : ['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'];
    }

    public function render(): View
    {
        return view('components.admin.course-stats');
    }
}
