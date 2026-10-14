<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Topic;
use App\Support\AdminCourseFormData;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('admin.dashboard');
    }

    public function create(): View
    {
        $teachers = Teacher::query()->orderBy('teacher_name')->get();
        $topics = Topic::query()->orderBy('name')->get();
        $courses = Course::query()->orderBy('course_code')->get();

        return view('admin.courses.create', [
            'teachers' => $teachers,
            'topics' => $topics,
            'courses' => $courses,
            'majors' => Course::MAJORS,
            'types' => Course::TYPES,
            'selectedTeachers' => AdminCourseFormData::selectedTeachers($teachers, (array) old('teacher_ids', [])),
            'selectedTopics' => AdminCourseFormData::selectedTopics($topics, (array) old('topic_ids', [])),
            'selectedPrerequisites' => AdminCourseFormData::selectedPrerequisites($courses, (array) old('prerequisite_ids', [])),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCourse($request);
        $relations = $this->extractRelations($validated);

        $course = DB::transaction(function () use ($request, $validated, $relations): Course {
            $courseData = $this->prepareImage($request, $validated);
            $course = Course::create($courseData + [
                'is_offered' => true,
                'publication_status' => Course::PUBLICATION_DRAFT,
            ]);
            $this->syncRelations($course, $relations);

            return $course;
        });

        return to_route('admin.courses.edit', $course)
            ->with('success', 'เพิ่มรายวิชาเรียบร้อยแล้ว');
    }

    public function edit(Course $course): View
    {
        $course = Course::query()->withStatistics()
            ->with(['teachers', 'topics', 'prerequisiteCourses', 'dependentCourses'])
            ->findOrFail($course->getKey());
        $gradeCounts = $course->reportedGradeCounts();

        return view('admin.courses.edit', [
            'course' => $course,
            'gradeCounts' => $gradeCounts,
            'teachers' => Teacher::query()->orderBy('teacher_name')->get(),
            'topics' => Topic::query()->orderBy('name')->get(),
            'courses' => Course::query()
                ->whereKeyNot($course->getKey())
                ->orderBy('course_code')
                ->get(),
            'relations' => AdminCourseFormData::relations($course),
            'majorOptions' => Course::MAJORS,
            'typeOptions' => AdminCourseFormData::typeOptions(),
            'offeredOptions' => AdminCourseFormData::offeredOptions(),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $this->validateCourse($request, $course);
        $relations = $this->extractRelations($validated);

        DB::transaction(function () use ($request, $course, $validated, $relations): void {
            $course->update($this->prepareImage($request, $validated, $course));
            $this->syncRelations($course, $relations);
        });

        return to_route('admin.courses.edit', $course)
            ->with('success', 'แก้ไขรายวิชาเรียบร้อยแล้ว');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return to_route('admin.dashboard')->with('success', 'ลบรายวิชาเรียบร้อยแล้ว');
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('keyword', ''));

        return response()->json(
            Course::query()
                ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                    $query->where(function (Builder $query) use ($keyword): void {
                        $query->where('course_code', 'like', "%{$keyword}%")
                            ->orWhere('course_name_th', 'like', "%{$keyword}%")
                            ->orWhere('course_name_en', 'like', "%{$keyword}%");
                    });
                })
                ->orderBy('course_code')
                ->limit(10)
                ->get(['id', 'course_code', 'course_name_th', 'course_name_en']),
        );
    }

    public function inlineUpdate(Request $request, Course $course): JsonResponse
    {
        $validated = $this->validateInline($request, $course);
        $relations = $this->extractRelations($validated);
        $courseData = collect($validated)->except(array_keys($relations))->all();

        DB::transaction(function () use ($course, $courseData, $relations): void {
            if ($courseData !== []) {
                $course->update($courseData);
            }
            $this->syncRelations($course, $relations);
        });

        $displays = collect($courseData)->mapWithKeys(
            fn (mixed $value, string $key): array => [
                $key => match ($key) {
                    'major' => Course::MAJORS[$value] ?? $value,
                    'is_offered' => $value ? 'เปิดสอน' : 'ยังไม่เปิดสอน',
                    'publication_status' => Course::PUBLICATION_STATUS_LABELS[$value] ?? $value,
                    default => $value,
                },
            ],
        );

        return response()->json(['ok' => true, 'displays' => $displays]);
    }

    public function updateImage(Request $request, Course $course): JsonResponse
    {
        $request->validate(
            ['image' => ['required', 'image', 'max:2048']],
            $this->imageValidationMessages(),
        );

        $path = $request->file('image')->store('courses', 'public');

        if ($imagePath = $course->imageStoragePath()) {
            Storage::disk('public')->delete($imagePath);
        }

        $course->update(['image' => $path]);

        return response()->json(['ok' => true, 'url' => $course->fresh()->imageUrl()]);
    }

    private function validateCourse(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'course_name_en' => ['required', 'string', 'max:255'],
            'course_name_th' => ['required', 'string', 'max:255'],
            'course_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'course_code')->ignore($course),
            ],
            'description' => ['nullable', 'string'],
            'major' => ['required', Rule::in(array_keys(Course::MAJORS))],
            'course_type' => ['required', Rule::in(Course::TYPES)],
            'credits' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_offered' => ['sometimes', 'boolean'],
            'publication_status' => ['sometimes', Rule::in(Course::PUBLICATION_STATUSES)],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['integer', 'distinct', 'exists:teachers,id'],
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['integer', 'distinct', 'exists:topics,id'],
            'prerequisite_ids' => ['nullable', 'array'],
            'prerequisite_ids.*' => [
                'integer',
                'distinct',
                'exists:courses,id',
                Rule::notIn($course?->getKey()),
            ],
        ], $this->imageValidationMessages());
    }

    private function validateInline(Request $request, Course $course): array
    {
        return $request->validate([
            'course_name_en' => ['sometimes', 'required', 'string', 'max:255'],
            'course_name_th' => ['sometimes', 'required', 'string', 'max:255'],
            'course_code' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'course_code')->ignore($course),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'major' => ['sometimes', Rule::in(array_keys(Course::MAJORS))],
            'course_type' => ['sometimes', Rule::in(Course::TYPES)],
            'credits' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_offered' => ['sometimes', 'boolean'],
            'publication_status' => ['sometimes', Rule::in(Course::PUBLICATION_STATUSES)],
            'teacher_ids' => ['sometimes', 'array'],
            'teacher_ids.*' => ['integer', 'distinct', 'exists:teachers,id'],
            'topic_ids' => ['sometimes', 'array'],
            'topic_ids.*' => ['integer', 'distinct', 'exists:topics,id'],
            'prerequisite_ids' => ['sometimes', 'array'],
            'prerequisite_ids.*' => [
                'integer',
                'distinct',
                'exists:courses,id',
                Rule::notIn($course->getKey()),
            ],
        ]);
    }

    private function prepareImage(Request $request, array $data, ?Course $course = null): array
    {
        if (! $request->hasFile('image')) {
            return collect($data)->except('image')->all();
        }

        if ($imagePath = $course?->imageStoragePath()) {
            Storage::disk('public')->delete($imagePath);
        }

        $data['image'] = $request->file('image')->store('courses', 'public');

        return $data;
    }

    private function extractRelations(array &$data): array
    {
        return collect(['teacher_ids', 'topic_ids', 'prerequisite_ids'])
            ->mapWithKeys(function (string $key) use (&$data): array {
                $relation = match ($key) {
                    'teacher_ids' => 'teachers',
                    'topic_ids' => 'topics',
                    'prerequisite_ids' => 'prerequisiteCourses',
                };
                $ids = $data[$key] ?? [];
                unset($data[$key]);

                return [$relation => $ids];
            })
            ->all();
    }

    private function syncRelations(Course $course, array $relations): void
    {
        foreach ($relations as $relation => $ids) {
            $course->{$relation}()->sync($ids);
        }
    }

    private function imageValidationMessages(): array
    {
        return [
            'image.uploaded' => 'อัปโหลดรูปภาพไม่สำเร็จ กรุณาเลือกไฟล์ขนาดไม่เกิน 2 MB แล้วลองใหม่',
            'image.image' => 'ไฟล์ที่เลือกต้องเป็นรูปภาพเท่านั้น',
            'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 2 MB',
        ];
    }
}
