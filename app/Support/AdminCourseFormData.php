<?php

namespace App\Support;

use App\Models\Course;
use Illuminate\Support\Collection;

final class AdminCourseFormData
{
    public static function selectedTeachers(Collection $teachers, array $ids): array
    {
        return $teachers
            ->whereIn('id', $ids)
            ->map(fn ($teacher): array => [
                'id' => $teacher->id,
                'label' => trim($teacher->prefix.' '.$teacher->teacher_name),
            ])
            ->values()
            ->all();
    }

    public static function selectedTopics(Collection $topics, array $ids): array
    {
        return $topics
            ->whereIn('id', $ids)
            ->map(fn ($topic): array => [
                'id' => $topic->id,
                'label' => $topic->name,
            ])
            ->values()
            ->all();
    }

    public static function selectedPrerequisites(Collection $courses, array $ids): array
    {
        return $courses
            ->whereIn('id', $ids)
            ->map(fn ($course): array => self::courseItem($course))
            ->values()
            ->all();
    }

    public static function relations(Course $course): array
    {
        return [
            [
                'field' => 'teacher_ids',
                'title' => 'อาจารย์ผู้สอน',
                'empty' => 'ไม่ระบุชื่ออาจารย์ผู้สอน',
                'items' => $course->teachers->map(fn ($teacher): array => [
                    'id' => $teacher->id,
                    'label' => trim($teacher->prefix.' '.$teacher->teacher_name),
                ])->values()->all(),
                'url' => route('admin.teachers.search'),
                'addLabel' => '+ เพิ่มอาจารย์',
                'placeholder' => 'พิมพ์ชื่ออาจารย์เพื่อค้นหา',
                'labelKeys' => 'prefix,teacher_name',
                'classes' => 'group/relation rounded-[22px] border border-slate-200 bg-white p-6',
            ],
            [
                'field' => 'topic_ids',
                'title' => 'หัวข้อที่เกี่ยวข้อง',
                'empty' => 'ยังไม่มีหัวข้อในรายวิชานี้',
                'items' => $course->topics->map(fn ($topic): array => [
                    'id' => $topic->id,
                    'label' => $topic->name,
                ])->values()->all(),
                'url' => route('admin.topics.search'),
                'addLabel' => '+ เพิ่มหัวข้อ',
                'placeholder' => 'พิมพ์ชื่อหัวข้อเพื่อค้นหา',
                'labelKeys' => 'name',
                'classes' => 'group/relation rounded-[22px] border border-slate-200 bg-white p-6',
            ],
            [
                'field' => 'prerequisite_ids',
                'title' => 'วิชาบังคับก่อน (Previous Course)',
                'empty' => 'ไม่มีวิชาบังคับก่อน',
                'items' => $course->prerequisiteCourses->map(fn ($prerequisite): array => self::courseItem($prerequisite))->values()->all(),
                'url' => route('admin.courses.search'),
                'placeholder' => 'ค้นหารหัสวิชา หรือชื่อวิชาภาษาอังกฤษ...',
                'labelKeys' => 'course_code',
                'subKeys' => 'course_name_en',
                'alwaysOpen' => true,
                'wide' => true,
                'classes' => 'group/relation rounded-[22px] border border-slate-200 bg-white p-6 md:col-span-2',
            ],
        ];
    }

    public static function typeOptions(): array
    {
        return array_combine(Course::TYPES, Course::TYPES);
    }

    public static function offeredOptions(): array
    {
        return [1 => 'เปิดสอน', 0 => 'ยังไม่เปิดสอน'];
    }

    private static function courseItem(Course $course): array
    {
        return [
            'id' => $course->id,
            'label' => $course->course_code,
            'sublabel' => $course->course_name_en,
        ];
    }
}
