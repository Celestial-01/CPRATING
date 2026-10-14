<?php

namespace App\View\Components\Admin;

use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

final class CourseStats extends Component
{
    /**
     * @var list<array{title: string, average: ?float, percentage: ?int}>
     */
    public array $metrics;

    public ?float $averageGradePoint;

    public int $gradeSampleSize;

    public function __construct(
        public Course $course,
        public Collection $gradeCounts,
    ) {
        $this->metrics = [
            [
                'title' => 'ความยากของวิชา',
                'average' => $course->difficulty_average === null ? null : (float) $course->difficulty_average,
                'percentage' => $course->difficultyPercentage(),
            ],
            [
                'title' => 'ปริมาณงานและการบ้าน',
                'average' => $course->workload_average === null ? null : (float) $course->workload_average,
                'percentage' => $course->workloadPercentage(),
            ],
        ];
        $this->averageGradePoint = $course->averageGradePoint($gradeCounts);
        $this->gradeSampleSize = (int) $gradeCounts->sum();
    }

    public function render(): View
    {
        return view('components.admin.course-stats');
    }
}
