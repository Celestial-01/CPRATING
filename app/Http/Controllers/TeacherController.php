<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TeacherController extends Controller
{
   
    public function index(): View
    {
        $teachers = Teacher::all();

        return view('teachers.index', compact('teachers'));
    }

   
   public function create(): View
    {
        return view('teachers.create');
    }

    public function edit(Teacher $teacher): View
    {
        return view('teachers.create', ['teacher' => $teacher,]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prefix' => ['nullable', 'string', 'max:50'],
            'teacher_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('teachers', 'public');
        }

        Teacher::create($validated);

        return redirect()
            ->route('teachers.index')
            ->with('success', 'เพิ่มอาจารย์เรียบร้อยแล้ว');
    }



  
    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'prefix' => ['nullable', 'string', 'max:50'],
            'teacher_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('teachers', 'public');
        }

        $teacher->update($validated);

        return redirect()
            ->route('teachers.index')
            ->with('success', 'แก้ไขข้อมูลอาจารย์เรียบร้อยแล้ว');
    }


    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()
            ->route('teachers.index')
            ->with('success', 'ลบอาจารย์เรียบร้อยแล้ว');
    }
    public function search(Request $request): JsonResponse
{
    $keyword = $request->input('keyword', '');

    $teachers = Teacher::where('teacher_name', 'like', "%{$keyword}%")
        ->orWhere('prefix', 'like', "%{$keyword}%")
        ->limit(10)
        ->get([
            'id',
            'prefix',
            'teacher_name',
        ]);

    return response()->json($teachers);
}
}