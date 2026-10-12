<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

final class MajorTabs extends Component
{
    public array $tabs;

    public function __construct(
        public array $majors,
        public string $current,
        public string $keyword = '',
        public array $types = [],
    ) {
        $this->tabs = collect($majors)->map(function (string $name, string $code): array {
            $active = $code === $this->current;

            return [
                'code' => $code,
                'name' => $name,
                'active' => $active,
                'url' => route('admin.dashboard', array_filter([
                    'major' => $code,
                    'q' => $this->keyword,
                    'types' => $this->types,
                ])),
                'classes' => 'flex h-11 items-center justify-center rounded-[10px] text-base font-semibold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-sky-100'.($active
                    ? ' bg-[#1684e5] text-white'
                    : ' bg-white text-slate-700 hover:text-[#1684e5]'),
            ];
        })->values()->all();
    }

    public function render(): View
    {
        return view('components.admin.major-tabs');
    }
}
