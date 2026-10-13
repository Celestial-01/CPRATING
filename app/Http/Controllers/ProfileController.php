<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasCompletedProfile()) {
            return redirect()->route('home');
        }

        return view('profile.complete', [
            'user' => $user->loadMissing('googleAccount'),
            'majorOptions' => Course::MAJORS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $validated = $request->validate([
            'student_id' => [
                'required',
                'string',
                'regex:/^\d{9}-\d$/',
                Rule::unique('users', 'student_id')->ignore($user),
            ],
            'major_code' => [
                'required',
                Rule::in(array_keys(Course::MAJORS)),
            ],
        ], [
            'student_id.regex' => 'กรุณากรอกรหัสนักศึกษาในรูปแบบ 69xxxxxxx-x',
            'student_id.unique' => 'รหัสนักศึกษานี้ถูกใช้งานแล้ว',
            'major_code.in' => 'กรุณาเลือกสาขาที่ถูกต้อง',
        ]);

        $user->forceFill($validated)->save();

        return redirect()->route('profile.complete.success');
    }

    public function success(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (! $user->hasCompletedProfile()) {
            return redirect()->route('profile.complete');
        }

        return view('profile.success', [
            'user' => $user->loadMissing('googleAccount'),
        ]);
    }
}
