<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('opening a published course records a view without changing its update time', function () {
    $course = Course::factory()->create([
        'course_code' => 'CP700001',
        'updated_at' => now()->subDay(),
    ]);
    $updatedAt = $course->updated_at;

    $this->get(route('courses.show', $course->course_code))
        ->assertOk()
        ->assertSee('เข้าชม')
        ->assertViewHas('course', fn (Course $shown): bool => $shown->views === 1);

    $this->get(route('courses.show', $course->course_code))
        ->assertOk()
        ->assertViewHas('course', fn (Course $shown): bool => $shown->views === 2);

    expect($course->fresh()->views)->toBe(2)
        ->and($course->fresh()->updated_at->equalTo($updatedAt))->toBeTrue();
});

test('drafts and loading placeholders do not receive views', function () {
    $published = Course::factory()->create(['course_code' => 'CP700002']);
    $draft = Course::factory()->draft()->create(['course_code' => 'CP700003']);

    $this->get(route('courses.show', $draft->course_code))->assertNotFound();
    $this->get(route('courses.show', ['course' => $published->course_code, 'loading' => 1]))
        ->assertOk();

    expect($draft->fresh()->views)->toBe(0)
        ->and($published->fresh()->views)->toBe(0);
});

test('the home page ranks popular published courses by views', function () {
    $mostViewed = Course::factory()->create(['course_code' => 'CP700004', 'views' => 12]);
    $lessViewed = Course::factory()->create(['course_code' => 'CP700005', 'views' => 3]);
    Course::factory()->draft()->create(['course_code' => 'CP700006', 'views' => 100]);

    $this->get(route('home'))
        ->assertOk()
        ->assertViewHas('popularCourses', fn ($courses): bool => $courses->pluck('course_code')->all() === [
            $mostViewed->course_code,
            $lessViewed->course_code,
        ]);
});
