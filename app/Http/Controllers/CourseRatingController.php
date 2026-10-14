<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseRatingController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->publication_status === Course::PUBLICATION_PUBLISHED, 404);

        $validated = $request->validateWithBag('courseRating', [
            'difficulty' => ['required', 'integer', 'between:1,5'],
            'workload' => ['required', 'integer', 'between:1,5'],
            'interest' => ['required', 'integer', 'between:1,5'],
            'grade' => ['nullable', 'string', Rule::in(array_keys(CourseRating::GRADE_POINTS))],
        ], [
            'required' => 'กรุณาให้คะแนน:attributeก่อนบันทึก',
            'integer' => 'คะแนน:attributeต้องเป็นจำนวนเต็ม',
            'between' => 'คะแนน:attributeต้องอยู่ระหว่าง 1 ถึง 5',
            'grade.string' => 'กรุณาเลือกเกรดจากรายการที่กำหนด',
            'grade.in' => 'กรุณาเลือกเกรดจากรายการที่กำหนด',
        ], [
            'difficulty' => 'ระดับความยาก',
            'workload' => 'ปริมาณงาน',
            'interest' => 'ความน่าสนใจของรายวิชา',
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

    public function destroy(Request $request, Course $course): RedirectResponse
    {
        $deletedRatings = $course->ratings()
            ->where('user_id', $request->user()->id)
            ->delete();

        $redirect = $course->publication_status === Course::PUBLICATION_PUBLISHED
            ? redirect()->route('courses.show', $course->course_code)
            : redirect()->route('courses.index');

        return $redirect
            ->with('rating_status', $deletedRatings > 0
                ? 'ยกเลิกรีวิวแล้ว'
                : 'คุณยังไม่ได้ให้คะแนนวิชานี้');
    }
}
