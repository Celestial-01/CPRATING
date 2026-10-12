<?php

namespace App\Http\Controllers;

use App\Models\CourseRating;
use App\Support\CourseCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourseRatingController extends Controller
{
    public function store(Request $request, string $code): RedirectResponse
    {
        $course = CourseCatalog::find($code);

        if ($course === null) {
            throw new NotFoundHttpException;
        }

        $rating = $request->validate([
            'difficulty' => ['required', 'integer', 'between:1,5'],
            'workload' => ['required', 'integer', 'between:1,5'],
            'interest' => ['required', 'integer', 'between:1,5'],
        ]);

        $savedRating = CourseRating::query()->createOrFirst(
            ['user_id' => $request->user()->id, 'course_code' => $course['code']],
            $rating,
        );

        return redirect()
            ->route('courses.show', $course['code'])
            ->with('rating_status', $savedRating->wasRecentlyCreated
                ? 'บันทึกคะแนนแล้ว'
                : 'คุณให้คะแนนวิชานี้ไปแล้ว');
    }
}
