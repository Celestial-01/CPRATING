<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

final class InlineField extends Component
{
    public mixed $value;

    public mixed $shown;

    public string $controlClasses = 'w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100';

    public function __construct(
        public mixed $course,
        public string $field,
        public string $type = 'text',
        public array $options = [],
        public ?string $label = null,
        public ?string $suffix = null,
        public string $placeholder = 'ยังไม่มีข้อมูล',
        public string $displayClass = 'text-sm text-slate-800',
    ) {
        $this->value = $course->{$field};

        if (is_bool($this->value)) {
            $this->value = (int) $this->value;
        }

        $this->shown = $type === 'select'
            ? ($options[$this->value] ?? $this->value)
            : $this->value;
    }

    public function render(): View
    {
        return view('components.admin.inline-field');
    }
}
