<?php

namespace App\View\Components\Admin;

use App\Models\Teacher;
use Illuminate\View\Component;
use Illuminate\View\View;

final class TeacherForm extends Component
{
    public bool $editing;

    public string $pageTitle;

    public string $pageDescription;

    public string $formAction;

    public string $submitLabel;

    public string $selectedPrefix;

    public string $teacherName;

    public function __construct(
        public array $prefixes,
        public ?Teacher $teacher = null,
    ) {
        $this->editing = $teacher !== null;
        $this->pageTitle = $this->editing ? 'แก้ไขอาจารย์' : 'เพิ่มอาจารย์';
        $this->pageDescription = $this->editing ? 'แก้ไขข้อมูลอาจารย์ผู้สอน' : 'กรอกข้อมูลอาจารย์ผู้สอน';
        $this->formAction = $this->editing
            ? route('admin.teachers.update', $teacher)
            : route('admin.teachers.store');
        $this->submitLabel = $this->editing ? 'บันทึกการแก้ไข' : 'บันทึกอาจารย์';
        $this->selectedPrefix = old('prefix', $teacher?->prefix ?? '');
        $this->teacherName = old('teacher_name', $teacher?->teacher_name ?? '');
    }

    public function render(): View
    {
        return view('components.admin.teacher-form');
    }
}
