<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::query()->orderBy('teacher_name')->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        $prefixes = Teacher::PREFIXES;

        return view('admin.teachers.create', compact('prefixes'));
    }

    public function edit(Teacher $teacher): View
    {
        $prefixes = Teacher::PREFIXES;

        return view('admin.teachers.edit', compact('teacher', 'prefixes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prefix' => ['nullable', 'string', 'max:50'],
            'teacher_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('teachers', 'public');
        }

        Teacher::create($validated);

        return to_route('admin.teachers.index')->with('success', 'เพิ่มอาจารย์เรียบร้อยแล้ว');
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'prefix' => ['nullable', 'string', 'max:50'],
            'teacher_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('teachers', 'public');
        }

        $teacher->update($validated);

        return to_route('admin.teachers.index')->with('success', 'แก้ไขข้อมูลอาจารย์เรียบร้อยแล้ว');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return to_route('admin.teachers.index')->with('success', 'ลบอาจารย์เรียบร้อยแล้ว');
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['keyword' => ['nullable', 'string', 'max:100']]);
        $keyword = trim((string) $request->input('keyword', ''));

        $teachers = Teacher::query()
            ->where(function ($query) use ($keyword): void {
                $query
                    ->where('teacher_name', 'like', "%{$keyword}%")
                    ->orWhere('prefix', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get(['id', 'prefix', 'teacher_name']);

        return response()->json($teachers);
    }
}
