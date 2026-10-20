<?php

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('course history requires login and remains separate for each account', function () {
    $course = Course::factory()->create(['course_code' => 'CP900001']);
    $first = User::factory()->create();
    $second = User::factory()->create();

    $this->get(route('history.index'))->assertRedirect(route('auth.login'));
    $this->get(route('courses.show', $course->course_code))->assertOk();
    $this->assertDatabaseCount('user_course_views', 0);

    $this->actingAs($first)->get(route('courses.show', $course->course_code))->assertOk();
    $this->assertDatabaseHas('user_course_views', [
        'user_id' => $first->id,
        'course_id' => $course->id,
    ]);
    $this->get(route('history.index'))->assertOk()
        ->assertViewHas('viewedCourses', fn ($courses): bool => $courses->pluck('id')->all() === [$course->id]);

    $this->actingAs($second)->get(route('history.index'))->assertOk()
        ->assertViewHas('viewedCourses', fn ($courses): bool => $courses->isEmpty());
});

test('saving a published course belongs only to the signed-in user', function () {
    $course = Course::factory()->create(['course_code' => 'CP900002']);
    $first = User::factory()->create();
    $second = User::factory()->create();

    $this->postJson(route('courses.save', $course->course_code))->assertUnauthorized();

    $this->actingAs($first)->postJson(route('courses.save', $course->course_code))
        ->assertOk()->assertJsonPath('saved', true);
    $this->postJson(route('courses.save', $course->course_code))->assertOk();
    $this->assertDatabaseCount('user_saved_courses', 1);
    $this->get(route('history.index'))->assertViewHas(
        'savedCourses', fn ($courses): bool => $courses->pluck('id')->all() === [$course->id],
    );

    $this->actingAs($second)->get(route('history.index'))->assertViewHas(
        'savedCourses', fn ($courses): bool => $courses->isEmpty(),
    );
    $this->deleteJson(route('courses.unsave', $course->course_code))->assertOk();
    $this->assertDatabaseCount('user_saved_courses', 1);

    $this->actingAs($first)->deleteJson(route('courses.unsave', $course->course_code))
        ->assertOk()->assertJsonPath('saved', false);
    $this->assertDatabaseCount('user_saved_courses', 0);
});
