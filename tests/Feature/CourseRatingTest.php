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
        ->assertSessionHasErrors(['difficulty', 'workload', 'interest']);

    $this->assertDatabaseCount('course_ratings', 0);

    $this->post(route('courses.rating.store', 'CP000000'), [
        'difficulty' => 3,
        'workload' => 3,
        'interest' => 3,
    ])->assertNotFound();
});

test('a user can rate a course only once and sees the completed state', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertSee('data-course-rating-dialog', false)
        ->assertSee('data-course-rating-open', false)
        ->assertSee('name="difficulty"', false)
        ->assertSee('name="workload"', false)
        ->assertSee('name="interest"', false);

    $this->actingAs($user)->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 4,
        'workload' => 3,
        'interest' => 5,
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->assertDatabaseHas('course_ratings', [
        'user_id' => $user->id,
        'course_code' => 'CP354101',
        'difficulty' => 4,
        'workload' => 3,
        'interest' => 5,
    ]);

    $this->get(route('courses.show', 'CP354101'))
        ->assertOk()
        ->assertSee('ให้คะแนนแล้ว')
        ->assertSee('data-rated="true"', false)
        ->assertDontSee('data-course-rating-dialog', false)
        ->assertSee('80%')
        ->assertSee('60%');

    $this->post(route('courses.rating.store', 'CP354101'), [
        'difficulty' => 1,
        'workload' => 1,
        'interest' => 1,
    ])->assertRedirect(route('courses.show', 'CP354101'));

    $this->assertDatabaseCount('course_ratings', 1);
    expect(CourseRating::query()->first()->interest)->toBe(5);
});
