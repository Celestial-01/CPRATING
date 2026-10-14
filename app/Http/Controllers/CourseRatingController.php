<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseRatingController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->publication_status === Course::PUBLICATION_PUBLISHED, 404);

        $validated = $request->validate([
            'difficulty' => ['required', 'integer', 'between:1,5'],
            'workload' => ['required', 'integer', 'between:1,5'],
            'interest' => ['required', 'integer', 'between:1,5'],
        ]);

        $savedRating = $course->ratings()->createOrFirst(
            ['user_id' => $request->user()->id],
            $validated,
        );

        return redirect()
            ->route('courses.show', $course->course_code)
            ->with('rating_status', $savedRating->wasRecentlyCreated
                ? 'บันทึกคะแนนแล้ว'
                : 'คุณให้คะแนนวิชานี้ไปแล้ว');
    }


}
