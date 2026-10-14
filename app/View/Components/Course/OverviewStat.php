<?php

namespace App\View\Components\Course;

use App\Models\Course;
use Illuminate\View\Component;
use Illuminate\View\View;

final class OverviewStat extends Component
{
    public function __construct(
        public Course $course,
        public string $metric,
    ) {}

    public function title(): string
    {
        return match ($this->metric) {
            'difficulty' => 'ระดับความยาก',
            'workload' => 'ปริมาณงาน',
            default => '',
        };
    }

    public function percentage(): ?int
    {
        return match ($this->metric) {
            'difficulty' => $this->course->difficultyPercentage(),
            'workload' => $this->course->workloadPercentage(),
            default => null,
        };
    }

    public function accentClass(): string
    {
        return match ($this->metric) {
            'difficulty' => 'bg-rose-400',
            'workload' => 'bg-[#477ff3]',
            default => 'bg-slate-400',
        };
    }

    public function summary(): string
    {
        return match (true) {
            $this->percentage() === null => 'ยังไม่มีคะแนน',
            $this->percentage() >= 65 => $this->metric === 'difficulty'
                ? 'ยาก'
                : 'ค่อนข้างหนัก',
            $this->percentage() >= 40 => 'ปานกลาง',
            default => 'น้อย',
        };
    }

    public function description(): string
    {
        return $this->course->ratings_count > 0
            ? 'เฉลี่ยจากผู้ให้คะแนน '.number_format($this->course->ratings_count).' คน'
            : 'ยังไม่มีผู้ให้คะแนน';
    }

    public function render(): View
    {
        return view('components.course.overview-stat');
    }
}
