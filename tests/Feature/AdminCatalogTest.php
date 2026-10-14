<?php

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('the admin create page submits to the course store route', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.courses.create'))
        ->assertOk()
        ->assertSee('action="'.route('admin.courses.store').'"', false)
        ->assertSee('href="'.route('admin.dashboard').'"', false)
        ->assertSee('enctype="multipart/form-data"', false);
});

test('an admin can create a course and sync its relations', function () {
    $admin = User::factory()->admin()->create();
    $teacher = Teacher::factory()->create();
    $topic = Topic::factory()->create();
    $prerequisite = Course::factory()->create(['course_code' => 'CP100001']);

    $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
        'course_name_en' => 'Database Systems',
        'course_name_th' => 'ระบบฐานข้อมูล',
        'course_code' => 'CP200001',
        'description' => 'Course description',
        'major' => 'CS',
        'course_type' => Course::TYPES[0],
        'credits' => 3,
        'teacher_ids' => [$teacher->id],
        'topic_ids' => [$topic->id],
        'prerequisite_ids' => [$prerequisite->id],
    ]);

    $course = Course::query()->where('course_code', 'CP200001')->firstOrFail();

    $response->assertRedirect(route('admin.courses.edit', $course));
    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'publication_status' => Course::PUBLICATION_DRAFT,
        'is_offered' => 1,
    ]);
    expect($course->teachers()->whereKey($teacher)->exists())->toBeTrue()
        ->and($course->topics()->whereKey($topic)->exists())->toBeTrue()
        ->and($course->prerequisiteCourses()->whereKey($prerequisite)->exists())->toBeTrue();
});

test('course code validation matches the eight-character database column', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::factory()->create(['course_code' => 'CP200005']);

    $this->actingAs($admin)
        ->post(route('admin.courses.store'), [
            'course_name_en' => 'Invalid Code',
            'course_name_th' => 'รหัสวิชาไม่ถูกต้อง',
            'course_code' => 'CP2000005',
            'major' => 'CS',
            'course_type' => Course::TYPES[0],
            'credits' => 3,
        ])
        ->assertSessionHasErrors('course_code');

    $this->actingAs($admin)
        ->patchJson(route('admin.courses.inline-update', $course), [
            'course_code' => 'CP2000005',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('course_code');

    expect($course->fresh()->course_code)->toBe('CP200005');
    $this->assertDatabaseMissing('courses', ['course_code' => 'CP2000005']);
});

test('an admin can upload a valid course image', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $courseType = Course::TYPES[0];

    $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
        'course_name_en' => 'Uploaded Image Course',
        'course_name_th' => 'เธงเธดเธเธฒเธ—เธ”เธชเธญเธเธญเธฑเธเนเธซเธฅเธ”',
        'course_code' => 'CP200003',
        'description' => 'Course description',
        'major' => 'CS',
        'course_type' => $courseType,
        'credits' => 3,
        'image' => UploadedFile::fake()->image('course-cover.jpg')->size(26),
    ]);

    $course = Course::query()->where('course_code', 'CP200003')->firstOrFail();

    $response->assertRedirect(route('admin.courses.edit', $course));
    expect($course->imageStoragePath())->toStartWith('courses/');
    Storage::disk('public')->assertExists($course->imageStoragePath());
});

test('an admin can edit a course inline and receives display values', function () {
    $admin = User::factory()->admin()->create();
    $teacher = Teacher::factory()->create();
    $topic = Topic::factory()->create();
    $prerequisite = Course::factory()->create(['course_code' => 'CP100002']);
    $course = Course::factory()->create(['course_code' => 'CP200002']);
    $course->teachers()->attach($teacher);
    $course->topics()->attach($topic);
    $course->prerequisiteCourses()->attach($prerequisite);

    $this->actingAs($admin)
        ->get(route('admin.courses.edit', $course))
        ->assertOk()
        ->assertViewIs('admin.courses.edit')
        ->assertSee('data-field="teacher_ids"', false)
        ->assertSee('data-field="topic_ids"', false)
        ->assertSee('data-field="prerequisite_ids"', false)
        ->assertSee('data-label="'.$teacher->display_name.'"', false)
        ->assertSee('data-label="'.$prerequisite->course_code.'"', false);

    $this->actingAs($admin)
        ->patchJson(route('admin.courses.inline-update', $course), [
            'course_name_th' => 'ชื่อวิชาใหม่',
            'is_offered' => false,
        ])
        ->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('displays.course_name_th', 'ชื่อวิชาใหม่')
        ->assertJsonPath('displays.is_offered', 'ยังไม่เปิดสอน');

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'course_name_th' => 'ชื่อวิชาใหม่',
        'is_offered' => 0,
    ]);
    $updatedCourse = $course->fresh();

    expect($updatedCourse->teachers()->whereKey($teacher)->exists())->toBeTrue()
        ->and($updatedCourse->topics()->whereKey($topic)->exists())->toBeTrue()
        ->and($updatedCourse->prerequisiteCourses()->whereKey($prerequisite)->exists())->toBeTrue();
});

test('publishing a draft makes it available in the public catalog', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::factory()->draft()->create(['course_code' => 'CP200004']);

    $this->actingAs($admin)
        ->patchJson(route('admin.courses.inline-update', $course), [
            'publication_status' => Course::PUBLICATION_PUBLISHED,
        ])
        ->assertOk()
        ->assertJsonPath(
            'displays.publication_status',
            Course::PUBLICATION_STATUS_LABELS[Course::PUBLICATION_PUBLISHED],
        );

    $this->get(route('courses.index'))
        ->assertOk()
        ->assertSee($course->course_code);
});
