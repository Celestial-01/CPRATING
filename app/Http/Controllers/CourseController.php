<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::with(['teachers', 'topics'])->get();

        return view('courses.index', compact('courses'));
    }


    public function create(): View
    {
        $teachers = Teacher::all();
        $topics = Topic::all();

       
        $courses = Course::orderBy('course_code')->get();

        $majors = Course::MAJORS;
        $types = Course::TYPES;

        return view('courses.create', compact(
            'teachers',
            'topics',
            'courses',
            'majors',
            'types'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_name_en' => ['required', 'string', 'max:255'],
            'course_name_th' => ['required', 'string', 'max:255'],
            'course_code' => ['required', 'string', 'max:8'],
            'description' => ['nullable', 'string'],
            'major' => ['required', 'in:' . implode(',', array_keys(Course::MAJORS))],
            'course_type' => ['required', 'in:' . implode(',', Course::TYPES)],
            'credits' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],

            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['required', 'integer', 'distinct', 'exists:teachers,id'],

            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['required', 'integer', 'distinct', 'exists:topics,id'],

            'prerequisite_ids' => ['nullable', 'array'],
            'prerequisite_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:courses,id',
            ],
        ]);

       
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        
        $teacherIds = $validated['teacher_ids'] ?? [];
        $topicIds = $validated['topic_ids'] ?? [];
        $prerequisiteIds = $validated['prerequisite_ids'] ?? [];

        unset(
            $validated['teacher_ids'],
            $validated['topic_ids'],
            $validated['prerequisite_ids']
        );

        
        if (in_array(0, $prerequisiteIds, true)) {
            return back()
                ->withInput()
                ->withErrors(['prerequisite_ids' => 'กรุณาเลือกวิชาบังคับก่อนที่ถูกต้อง']);
        }

        $course = Course::create([
            ...$validated,
            'is_active' => true,
        ]);

        $course->teachers()->sync($teacherIds);
        $course->topics()->sync($topicIds);
        $course->previousCourses()->sync($prerequisiteIds);

        return redirect()
            ->route('courses.create')
            ->with('success', 'เพิ่มรายวิชาเรียบร้อยแล้ว');
    }


    public function show(Course $course): View
    {
        $course->load([
            'teachers',
            'topics',
            'previousCourses',
            'nextCourses',
        ]);

        return view('course.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $teachers = Teacher::all();
        $topics = Topic::all();
        $courses = Course::where('id', '!=', $course->id)->get();

        $course->load([
            'teachers',
            'topics',
            'previousCourses',
        ]);

        return view('courses.edit', compact(
            'course',
            'teachers',
            'topics',
            'courses'
        ));
    }

    
    public function update(Request $request, Course $course): RedirectResponse
        {
            // 1. ตรวจสอบข้อมูลที่ส่งมาจากฟอร์ม
            $validated = $request->validate([
                'course_name_en' => ['required', 'string', 'max:255'],
                'course_name_th' => ['required', 'string', 'max:255'],
                'course_code' => ['required', 'string', 'max:8'],
                'description' => ['nullable', 'string'],
                'major' => ['required', 'string'],
                'course_type' => ['required', 'string', 'max:255'],
                'image' => ['nullable', 'image', 'max:2048'],
                'credits' => ['required', 'integer'],
                'is_active' => ['required', 'boolean'],

                'teacher_ids' => ['nullable', 'array'],
                'teacher_ids.*' => ['exists:teachers,id'],

                'topic_ids' => ['nullable', 'array'],
                'topic_ids.*' => ['exists:topics,id'],

                'prerequisite_ids' => ['nullable', 'array'],
                'prerequisite_ids.*' => [
                    'exists:courses,id',
                    'not_in:' . $course->id,
                ],
            ]);

            // 2. แยก ID ของความสัมพันธ์ออกจากข้อมูลรายวิชา
            $teacherIds = $validated['teacher_ids'] ?? [];
            $topicIds = $validated['topic_ids'] ?? [];
            $prerequisiteIds = $validated['prerequisite_ids'] ?? [];

            unset(
                $validated['teacher_ids'],
                $validated['topic_ids'],
                $validated['prerequisite_ids']
            );

            // 3. ถ้ามีการอัปโหลดภาพใหม่ ให้บันทึกภาพ
            if ($request->hasFile('image')) {
                $validated['image'] = $request
                    ->file('image')
                    ->store('courses', 'public');
            } else {
                // ไม่อัปโหลดภาพใหม่ ให้ใช้ภาพเดิม
                unset($validated['image']);
            }

            // 4. อัปเดตข้อมูลหลักของรายวิชา
            $course->update($validated);

            // 5. อัปเดตความสัมพันธ์กับอาจารย์
            $course->teachers()->sync($teacherIds);

            // 6. อัปเดตความสัมพันธ์กับหัวข้อ
            $course->topics()->sync($topicIds);

            // 7. อัปเดตวิชาที่ต้องเรียนก่อน
            $course->previousCourses()->sync($prerequisiteIds);

            // 8. กลับไปหน้าจัดการรายวิชา พร้อมข้อความแจ้งผล
            return redirect()
                ->route('courses.edit', $course)
                ->with('success', 'แก้ไขรายวิชาเรียบร้อยแล้ว');
        }


    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()
            ->route('courses.index')
            ->with('success', 'ลบรายวิชา เรียบร้อยแล้ว');
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = $request->input('keyword', '');

        $courses = Course::where('course_name_en', 'like', "%{$keyword}%")
            ->orWhere('course_name_th', 'like', "%{$keyword}%")
            ->orWhere('course_code', 'like', "%{$keyword}%")
            ->limit(10)
            ->get([
                'id',
                'course_name_en',
                'course_name_th',
                'course_code',
            ]);

        return response()->json($courses);
    }
    public function inlineUpdate(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'course_name_en' => ['sometimes', 'required', 'string', 'max:255'],
            'course_name_th' => ['sometimes', 'required', 'string', 'max:255'],
            'course_code'    => ['sometimes', 'required', 'string', 'max:8'],
            'description'    => ['sometimes', 'nullable', 'string'],
            'major'          => ['sometimes', 'required', Rule::in(array_keys(Course::MAJORS))],
            'course_type'    => ['sometimes', 'required', Rule::in(Course::TYPES)],
            'credits'        => ['sometimes', 'required', 'integer', 'min:0'],
            'is_active'      => ['sometimes', 'required', 'boolean'],

            'teacher_ids'            => ['sometimes', 'array'],
            'teacher_ids.*'          => ['integer', 'distinct', 'exists:teachers,id'],
            'topic_ids'              => ['sometimes', 'array'],
            'topic_ids.*'            => ['integer', 'distinct', 'exists:topics,id'],
            'prerequisite_ids'       => ['sometimes', 'array'],
            'prerequisite_ids.*'     => ['integer', 'distinct', 'exists:courses,id', 'not_in:' . $course->id],
        ]);

        $relations = [
            'teacher_ids'      => 'teachers',
            'topic_ids'        => 'topics',
            'prerequisite_ids' => 'previousCourses',
        ];

        foreach ($relations as $key => $relation) {
            if (array_key_exists($key, $validated)) {
                $course->{$relation}()->sync($validated[$key]);
                unset($validated[$key]);
            }
        }

        if ($validated) {
            $course->update($validated);
        }

        $displays = [];
        foreach ($validated as $key => $value) {
            $displays[$key] = match ($key) {
                'major'     => Course::MAJORS[$value] ?? $value,
                'is_active' => $value ? 'เปิดสอน' : 'ยังไม่เปิดสอน',
                default     => $value,
            };
        }

        return response()->json(['ok' => true, 'displays' => $displays]);
    }

    public function updateImage(Request $request, Course $course): JsonResponse
    {
        $request->validate(['image' => ['required', 'image', 'max:2048']]);

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->update([
            'image' => $request->file('image')->store('courses', 'public'),
        ]);

        return response()->json(['ok' => true, 'url' => asset('storage/' . $course->image)]);
    }
}