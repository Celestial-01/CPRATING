<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
        $courses = Course::all();

        return view('courses.create', compact(
            'teachers',
            'topics',
            'courses'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
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
            'prerequisite_ids.*' => ['exists:courses,id'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('courses', 'public');
        }

        $course = Course::create($validated);

        $course->teachers()->sync($request->input('teacher_ids', []));
        $course->topics()->sync($request->input('topic_ids', []));
        $course->previousCourses()->sync($request->input('prerequisite_ids', []));

        return redirect()
            ->route('courses.index')
            ->with('success', 'สร้างรายวิชา สำเร็จ');
    }

    public function show(Course $course): View
    {
        $course->load([
            'teachers',
            'topics',
            'previousCourses',
            'nextCourses',
        ]);

        return view('courses.show', compact('course'));
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

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('courses', 'public');
        }

        $course->update($validated);

        $course->teachers()->sync($request->input('teacher_ids', []));
        $course->topics()->sync($request->input('topic_ids', []));
        $course->previousCourses()->sync(
            $request->input('prerequisite_ids', [])
        );

        return redirect()
            ->route('courses.index')
            ->with('success', 'แก้ไขรายวิชา เรียบร้อยแล้ว');
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
}