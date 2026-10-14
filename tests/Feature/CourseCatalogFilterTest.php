<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Course::factory()->create([
        'course_code' => 'CP352210',
        'major' => 'ITII',
    ]);
});

test('the course filter lists only the supported majors', function () {
    $majorCodes = ['CS', 'AI', 'CY', 'GIS', 'ITII'];

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
    $majorCodes = array_keys(Course::MAJORS);

    foreach (Course::query()->forPublicCatalog()->get() as $course) {
        expect($course->major)->toBeIn($majorCodes);
    }

    $this->get(route('courses.show', 'CP352210'))
        ->assertOk()
        ->assertViewHas('course', fn (Course $course): bool => $course->major === 'ITII');
});

test('course filter links reject obsolete or unsupported categories', function () {
    $this->get(route('courses.index', ['category' => 'CY']))
        ->assertOk()
        ->assertViewHas('selectedFilter', 'CY');

    $this->get(route('courses.index', ['category' => 'Programming']))
        ->assertOk()
        ->assertViewHas('selectedFilter', 'all');
});

test('search filtering and pagination query published courses on the server', function () {
    Course::factory()->create([
        'course_code' => 'CP777001',
        'course_name_th' => 'การออกแบบระบบ',
        'major' => 'CS',
    ]);
    Course::factory()->draft()->create([
        'course_code' => 'CP777002',
        'course_name_th' => 'การออกแบบระบบ',
        'major' => 'CS',
    ]);
    Course::factory()->create([
        'course_code' => 'CP777003',
        'course_name_th' => 'การออกแบบระบบ',
        'major' => 'AI',
    ]);

    $this->get(route('courses.index', ['q' => 'การออกแบบระบบ', 'category' => 'CS']))
        ->assertOk()
        ->assertSee('CP777001')
        ->assertDontSee('CP777002')
        ->assertDontSee('CP777003');

    Course::factory()->count(10)->create(['major' => 'CY']);

    $this->get(route('courses.index', ['category' => 'CY']))
        ->assertOk()
        ->assertViewHas('courses', fn ($courses): bool => $courses->total() === 10 && $courses->count() === 8);

    $this->get(route('courses.index', ['category' => 'CY', 'page' => 2]))
        ->assertOk()
        ->assertViewHas('courses', fn ($courses): bool => $courses->total() === 10 && $courses->count() === 2);
});

test('malformed search input is rejected', function () {
    $this->get(route('courses.index', ['q' => ['unexpected']]))
        ->assertSessionHasErrors('q');

    $this->get(route('courses.index', ['q' => str_repeat('x', 101)]))
        ->assertSessionHasErrors('q');
});
