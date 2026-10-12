<?php

use App\Models\Course;
use App\Support\CourseCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the course filter lists only the supported majors', function () {
    $majorCodes = ['CS', 'AI', 'CY', 'GIS', 'ITII'];

    expect(config('courses.major_codes'))->toBe($majorCodes);
    expect(array_diff($majorCodes, array_keys(Course::MAJORS)))->toBe([]);

    $response = $this->get(route('courses.index'))
        ->assertOk()
        ->assertViewHas('selectedFilter', 'all')
        ->assertSee('data-course-filter', false);

    foreach (['all', 'popular', ...$majorCodes] as $filter) {
        $response->assertSee('value="'.$filter.'"', false);
    }

    $response->assertDontSee('value="Programming"', false);
});

test('course tags use a supported major code', function () {
    $majorCodes = config('courses.major_codes');

    foreach (CourseCatalog::all() as $course) {
        expect($course['major_code'])->toBeIn($majorCodes);
    }

    $this->get(route('courses.show', 'CP352210'))
        ->assertOk()
        ->assertViewHas('course', fn (array $course): bool => $course['major_code'] === 'ITII');
});

test('course filter links reject obsolete or unsupported categories', function () {
    $this->get(route('courses.index', ['category' => 'CY']))
        ->assertOk()
        ->assertViewHas('selectedFilter', 'CY');

    $this->get(route('courses.index', ['category' => 'Programming']))
        ->assertOk()
        ->assertViewHas('selectedFilter', 'all');
});
