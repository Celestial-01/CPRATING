<?php

use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

uses(RefreshDatabase::class);

beforeEach(function () {
    Course::factory()->create(['course_code' => 'CP354101']);
});

test('guests must log in before rating a course', function () {
    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertSee('เข้าสู่ระบบเพื่อให้คะแนน')
        ->assertDontSee('data-course-rating-dialog', false);

    $this->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
    ])->assertRedirect(route('auth.login'));

    $this->assertDatabaseCount('course_ratings', 0);
});

test('a user can rate a course only once and sees the completed state', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertSee('data-course-rating-dialog', false)
        ->assertSee('data-course-rating-open', false)
        ->assertSee('name="difficulty"', false)
        ->assertSee('name="workload"', false)
        ->assertSee('name="interest"', false)
        ->assertSee('name="grade"', false)
        ->assertSee('เกรดที่ได้รับ')
        ->assertSee('ยังไม่ทราบเกรด / ไม่ระบุ');

    $this->actingAs($user)->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 4,
        'workload' => 3,
        'interest' => 5,
        'grade' => 'B+',
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->assertDatabaseHas('course_ratings', [
        'user_id' => $user->id,
        'course_code' => 'CP354101',
        'difficulty' => 4,
        'workload' => 3,
        'interest' => 5,
        'grade' => 'B+',
    ]);

    $response = $this->get(route('courses.show', 'CP354101'));

    $response
        ->assertOk()
        ->assertSee('ให้คะแนนแล้ว')
        ->assertSee('data-rated="true"', false)
        ->assertSee('data-course-rating-cancel-open', false)
        ->assertSee('ยืนยันยกเลิก')
        ->assertDontSee('data-course-rating-dialog', false)
        ->assertSee('80%')
        ->assertSee('60%');

    $this->assertSame(1, preg_match('/<button\b[^>]*class="course-rating-trigger[^"]*"[^>]*>/s', $response->getContent(), $ratingButton));
    $this->assertStringContainsString('data-course-rating-cancel-open', $ratingButton[0]);
    $this->assertStringNotContainsString('disabled', $ratingButton[0]);
    $this->assertSame(1, substr_count($response->getContent(), 'data-course-rating-cancel-open'));

    $this->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 1,
        'workload' => 1,
        'interest' => 1,
        'grade' => 'A',
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->assertDatabaseCount('course_ratings', 1);
    expect(CourseRating::query()->first()->interest)->toBe(5)
        ->and(CourseRating::query()->first()->grade)->toBe('B+');
});

test('ratings from different users determine the displayed averages', function () {
    CourseRating::query()->create([
        'user_id' => User::factory()->create()->id,
        'course_code' => 'CP354101',
        'difficulty' => 5,
        'workload' => 2,
        'interest' => 4,
    ]);
    CourseRating::query()->create([
        'user_id' => User::factory()->create()->id,
        'course_code' => 'CP354101',
        'difficulty' => 4,
        'workload' => 4,
        'interest' => 5,
    ]);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('course', fn (Course $course): bool => $course->interestRating() === 4.5
            && $course->difficultyPercentage() === 90
            && $course->workloadPercentage() === 60
            && $course->ratings_count === 2)
        ->assertSee('90%')
        ->assertSee('60%');
});

test('an unrated course shows no rating data instead of mock scores', function () {
    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('course', fn (Course $course): bool => $course->interestRating() === null
            && $course->difficultyPercentage() === null
            && $course->workloadPercentage() === null)
        ->assertSee('ยังไม่มีคะแนน')
        ->assertDontSee('74%');
});

test('rating values must be in the five star range and the course must exist', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('courses.rating.store', 'CP354101'), [
            'difficulty' => 0,
            'workload' => 6,
            'interest' => null,
        ])
        ->assertSessionHasErrorsIn('courseRating', ['difficulty', 'workload', 'interest']);

    $this->assertDatabaseCount('course_ratings', 0);

    $this->post(route('courses.rating.store', 'CP000000'), [
        'difficulty' => 3,
        'workload' => 3,
        'interest' => 3,
    ])->assertNotFound();
});

test('each supported grade can be saved and determines the average grade point', function (string $grade, float $gradePoint) {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => $grade,
    ])->assertRedirect(route('courses.show', 'CP354101'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('course_ratings', [
        'user_id' => $user->id,
        'course_code' => 'CP354101',
        'grade' => $grade,
    ]);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('gradeSampleSize', 1)
        ->assertViewHas('averageGradePoint', $gradePoint)
        ->assertSee(number_format($gradePoint, 2))
        ->assertSee('/ 4.00');
})->with([
    'A' => ['A', 4.0],
    'B+' => ['B+', 3.5],
    'B' => ['B', 3.0],
    'C+' => ['C+', 2.5],
    'C' => ['C', 2.0],
    'D+' => ['D+', 1.5],
    'D' => ['D', 1.0],
    'F' => ['F', 0.0],
]);

test('ratings may omit the grade without inventing grade statistics', function ($grade) {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => $grade,
    ])->assertRedirect(route('courses.show', 'CP354101'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('course_ratings', ['user_id' => $user->id, 'grade' => null]);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('gradeSampleSize', 0)
        ->assertViewHas('averageGradePoint', null)
        ->assertSee('เกรดเฉลี่ยของวิชานี้')
        ->assertSee('ยังไม่มีข้อมูลจากผู้ใช้')
        ->assertDontSee('/ 4.00')
        ->assertSee('data-progress-height="0"', false);
})->with([null, '']);

test('invalid grades are rejected and explained in Thai in the rating modal', function ($grade) {
    $this->actingAs(User::factory()->create())
        ->from(route('courses.show', 'CP354101'))
        ->post(route('courses.rating.store', 'CP354101'), [
            'difficulty' => 3,
            'workload' => 4,
            'interest' => 5,
            'grade' => $grade,
        ])
        ->assertRedirect(route('courses.show', 'CP354101'))
        ->assertSessionHasErrorsIn('courseRating', ['grade']);

    $this->assertDatabaseCount('course_ratings', 0);

    $this->withCookie(config('session.cookie'), session()->getId());

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('errors', fn ($errors): bool => $errors->getBag('courseRating')->has('grade'))
        ->assertSee('data-rating-errors', false)
        ->assertSee('กรุณาเลือกเกรดจากรายการที่กำหนด')
        ->assertSee('value="3"', false);
})->with(['A+', 'a', 'S', 5, [['A']]]);

test('malformed star inputs do not break rendering of validation feedback', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('courses.show', 'CP354101'))
        ->post(route('courses.rating.store', 'CP354101'), [
            'difficulty' => ['3'],
            'workload' => 'invalid',
            'interest' => null,
            'grade' => 'C+',
        ])
        ->assertSessionHasErrorsIn('courseRating', ['difficulty', 'workload', 'interest']);

    $this->withCookie(config('session.cookie'), session()->getId());

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertSee('data-rating-errors', false)
        ->assertSee('คะแนนระดับความยากต้องเป็นจำนวนเต็ม')
        ->assertSee('กรุณาให้คะแนนความน่าสนใจของรายวิชาก่อนบันทึก')
        ->assertSee('value="C+" selected', false);
});

test('comment validation errors do not open the course rating dialog', function () {
    $this->actingAs(User::factory()->create())
        ->withSession(['errors' => (new ViewErrorBag)
            ->put('default', new MessageBag(['content' => 'กรุณากรอกความคิดเห็น']))])
        ->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertSee('data-course-rating-dialog', false)
        ->assertDontSee('data-rating-errors', false);
});

test('grade statistics use only reported grades for the requested course', function () {
    foreach (['A', 'B+', 'F', null, 'W'] as $grade) {
        CourseRating::query()->create([
            'user_id' => User::factory()->create()->id,
            'course_code' => 'CP354101',
            'difficulty' => 3,
            'workload' => 4,
            'interest' => 5,
            'grade' => $grade,
        ]);
    }

    $otherCourse = Course::factory()->create(['course_code' => 'CP354102']);
    $otherCourse->ratings()->create([
        'user_id' => User::factory()->create()->id,
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => 'A',
    ]);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('gradeSampleSize', 3)
        ->assertViewHas('averageGradePoint', 2.5)
        ->assertViewHas('gradeCounts', fn ($gradeCounts): bool => $gradeCounts->get('A') === 1
            && $gradeCounts->get('B+') === 1
            && $gradeCounts->get('F') === 1
            && $gradeCounts->get('C') === 0
            && $gradeCounts->count() === 8)
        ->assertSee('33.3%')
        ->assertSee('2.50')
        ->assertSee('จากผู้รายงานเกรด 3 คน')
        ->assertSee('data-progress-height="33.3"', false)
        ->assertSee('คำนวณจากเกรดที่ผู้เรียนรายงาน')
        ->assertDontSee('โอกาสได้เกรด A');
});

test('average grade points are weighted by report counts and grade bars are ordered from A to F', function () {
    foreach (['A', 'A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F', null] as $grade) {
        CourseRating::query()->create([
            'user_id' => User::factory()->create()->id,
            'course_code' => 'CP354101',
            'difficulty' => 3,
            'workload' => 4,
            'interest' => 5,
            'grade' => $grade,
        ]);
    }

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('gradeSampleSize', 9)
        ->assertViewHas('averageGradePoint', 2.39)
        ->assertViewHas('gradeCounts', fn ($gradeCounts): bool => $gradeCounts->keys()->all()
            === ['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'])
        ->assertSee('2.39')
        ->assertSee('จากผู้รายงานเกรด 9 คน')
        ->assertSeeInOrder([
            'data-grade="A"',
            'data-grade="B+"',
            'data-grade="B"',
            'data-grade="C+"',
            'data-grade="C"',
            'data-grade="D+"',
            'data-grade="D"',
            'data-grade="F"',
        ], false);
});

test('a course with only F reports shows a zero average rather than a missing average', function () {
    CourseRating::query()->create([
        'user_id' => User::factory()->create()->id,
        'course_code' => 'CP354101',
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => 'F',
    ]);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('gradeSampleSize', 1)
        ->assertViewHas('averageGradePoint', 0.0)
        ->assertSee('0.00')
        ->assertSee('/ 4.00')
        ->assertDontSee('ยังไม่มีข้อมูลจากผู้ใช้');
});

test('users can cancel their rating and submit a new rating with a different grade', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => 'A',
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->delete(route('courses.rating.destroy', 'CP354101'))
        ->assertRedirect(route('courses.show', 'CP354101'))
        ->assertSessionHas('rating_status', 'ยกเลิกรีวิวแล้ว');

    $this->assertDatabaseCount('course_ratings', 0);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('hasRated', false)
        ->assertViewHas('gradeSampleSize', 0)
        ->assertViewHas('averageGradePoint', null)
        ->assertViewHas('course', fn (Course $course): bool => $course->interestRating() === null)
        ->assertSee('data-course-rating-open', false)
        ->assertSee('data-course-rating-dialog', false)
        ->assertDontSee('data-course-rating-cancel-open', false)
        ->assertDontSee('data-rated="true"', false);

    $this->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 1,
        'workload' => 2,
        'interest' => 3,
        'grade' => 'C',
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->assertDatabaseCount('course_ratings', 1);
    $this->assertDatabaseHas('course_ratings', ['user_id' => $user->id, 'grade' => 'C', 'interest' => 3]);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('gradeSampleSize', 1)
        ->assertViewHas('averageGradePoint', 2.0)
        ->assertViewHas('gradeCounts', fn ($gradeCounts): bool => $gradeCounts->get('C') === 1
            && $gradeCounts->get('A') === 0);
});

test('cancellation is scoped to the current user and course and keeps comments', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherCourse = Course::factory()->create(['course_code' => 'CP354102']);

    $rating = CourseRating::query()->create([
        'user_id' => $user->id,
        'course_code' => 'CP354101',
        'difficulty' => 5,
        'workload' => 5,
        'interest' => 5,
        'grade' => 'A',
    ]);
    $otherRating = CourseRating::query()->create([
        'user_id' => $otherUser->id,
        'course_code' => 'CP354101',
        'difficulty' => 1,
        'workload' => 1,
        'interest' => 1,
        'grade' => 'B',
    ]);
    $otherCourseRating = $otherCourse->ratings()->create([
        'user_id' => $user->id,
        'difficulty' => 2,
        'workload' => 2,
        'interest' => 2,
        'grade' => 'A',
    ]);
    $comment = Comment::query()->create([
        'user_id' => $user->id,
        'course_code' => 'CP354101',
        'content' => 'ความคิดเห็นที่ต้องเก็บไว้',
    ]);

    $this->actingAs($user)->delete(route('courses.rating.destroy', 'CP354101'), [
        'user_id' => $otherUser->id,
        'course_code' => $otherCourse->course_code,
        'rating_id' => $otherRating->id,
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->assertModelMissing($rating);
    $this->assertModelExists($otherRating);
    $this->assertModelExists($otherCourseRating);
    $this->assertModelExists($comment);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertViewHas('gradeSampleSize', 1)
        ->assertViewHas('averageGradePoint', 3.0)
        ->assertViewHas('course', fn (Course $course): bool => $course->interestRating() === 1.0
            && $course->difficultyPercentage() === 20
            && $course->workloadPercentage() === 20
            && $course->ratings_count === 1);
});

test('cancellation with no own rating or a repeated request does not delete another users rating', function () {
    $otherRating = CourseRating::query()->create([
        'user_id' => User::factory()->create()->id,
        'course_code' => 'CP354101',
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => 'A',
    ]);

    $this->actingAs(User::factory()->create());

    for ($attempt = 0; $attempt < 2; $attempt++) {
        $this->delete(route('courses.rating.destroy', 'CP354101'))
            ->assertRedirect(route('courses.show', 'CP354101'))
            ->assertSessionHas('rating_status', 'คุณยังไม่ได้ให้คะแนนวิชานี้');
    }

    $this->assertModelExists($otherRating);
    $this->assertDatabaseCount('course_ratings', 1);
});

test('users can cancel a rating after the course is hidden without being redirected to a missing page', function () {
    $course = Course::query()->where('course_code', 'CP354101')->firstOrFail();
    $user = User::factory()->create();
    $rating = $course->ratings()->create([
        'user_id' => $user->id,
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => 'A',
    ]);
    $course->update(['publication_status' => Course::PUBLICATION_HIDDEN]);

    $this->actingAs($user)->delete(route('courses.rating.destroy', 'CP354101'))
        ->assertRedirect(route('courses.index'))
        ->assertSessionHas('rating_status', 'ยกเลิกรีวิวแล้ว');

    $this->assertModelMissing($rating);

    $this->withCookie(config('session.cookie'), session()->getId())
        ->get(route('courses.index'))
        ->assertOk()
        ->assertSee('ยกเลิกรีวิวแล้ว');
});

test('guests cannot cancel ratings and cancellation requires an existing course', function () {
    $rating = CourseRating::query()->create([
        'user_id' => User::factory()->create()->id,
        'course_code' => 'CP354101',
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'grade' => 'A',
    ]);

    $this->delete(route('courses.rating.destroy', 'CP354101'))
        ->assertRedirect(route('auth.login'));
    $this->assertModelExists($rating);

    $this->actingAs(User::factory()->create())
        ->delete(route('courses.rating.destroy', 'CP000000'))
        ->assertNotFound();
});

test('rating storage ignores unvalidated ownership fields', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 3,
        'workload' => 4,
        'interest' => 5,
        'user_id' => $otherUser->id,
        'course_code' => 'CP000000',
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->assertDatabaseHas('course_ratings', [
        'user_id' => $user->id,
        'course_code' => 'CP354101',
    ]);
});
