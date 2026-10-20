<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavedCourseController extends Controller
{
    public function store(Request $request, Course $course): JsonResponse
    {
        Course::query()->published()->findOrFail($course->getKey());

        DB::table('user_saved_courses')->insertOrIgnore([
            'user_id' => $request->user()->getKey(),
            'course_id' => $course->getKey(),
            'created_at' => now(),
        ]);

        return response()->json(['saved' => true]);
    }

    public function destroy(Request $request, Course $course): JsonResponse
    {
        $request->user()->savedCourses()->detach($course->getKey());

        return response()->json(['saved' => false]);
    }
}
