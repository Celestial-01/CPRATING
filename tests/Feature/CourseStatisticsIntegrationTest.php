<?php

use App\Models\Course;
use App\Models\StudyPlan;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin publishing and user ratings feed the same statistics on every course page', function () {
    $admin = User::factory()->admin()->create();
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    $this->actingAs($admin)->post(route('admin.courses.store'), [
        'course_code' => 'CP800001',
        'course_name_en' => 'Integrated Course',
        'course_name_th' => 'รายวิชาเชื่อมข้อมูล',
        'major' => 'CS',
        'course_type' => Course::TYPES[0],
        'credits' => 3,
    ])->assertSessionHasNoErrors();

    $course = Course::query()->where('course_code', 'CP800001')->firstOrFail();
    $this->get(route('courses.index'))->assertDontSee('CP800001');
    $this->get(route('history.index'))->assertDontSee('CP800001');

    $this->patchJson(route('admin.courses.inline-update', $course), [
        'publication_status' => Course::PUBLICATION_PUBLISHED,
    ])->assertOk();

    $this->actingAs($firstUser)->post(route('courses.rating.store', $course->course_code), [
        'difficulty' => 5, 'workload' => 2, 'interest' => 4, 'grade' => 'A',
    ])->assertSessionHasNoErrors();
    $this->actingAs($secondUser)->post(route('courses.rating.store', $course->course_code), [
        'difficulty' => 4, 'workload' => 4, 'interest' => 5, 'grade' => 'F',
    ])->assertSessionHasNoErrors();

    $hasStatistics = fn (Course $shown): bool => $shown->interestRating() === 4.5
        && $shown->difficultyPercentage() === 90
        && $shown->workloadPercentage() === 60
        && $shown->ratings_count === 2;

    $this->get(route('courses.show', $course->course_code))
        ->assertOk()
        ->assertViewHas('course', $hasStatistics)
        ->assertViewHas('averageGradePoint', 2.0)
        ->assertSee('90%')->assertSee('60%');

    foreach (['courses.index', 'history.index'] as $route) {
        $this->get(route($route))->assertOk()
            ->assertViewHas('courses', fn ($courses): bool => $hasStatistics($courses->firstWhere('id', $course->id)));
    }

    $this->get(route('home'))->assertOk()
        ->assertViewHas('popularCourses', fn ($courses): bool => $hasStatistics($courses->firstWhere('id', $course->id)))
        ->assertViewHas('highlight', fn ($highlight): bool => (int) $highlight->rating_count === 2
            && (float) $highlight->rating === 4.5);

    $this->actingAs($admin)->get(route('admin.dashboard', ['major' => 'CS']))
        ->assertOk()
        ->assertViewHas('courses', fn ($courses): bool => $hasStatistics($courses->firstWhere('id', $course->id)))
        ->assertSee('4.5')->assertSee('1 วิว')->assertSee('2 รีวิว');

    $this->get(route('admin.courses.edit', $course))->assertOk()
        ->assertViewHas('course', $hasStatistics)
        ->assertSee('90%')->assertSee('60%')->assertSee('2.00')->assertSee('50%');

    $this->actingAs($firstUser)->delete(route('courses.rating.destroy', $course->course_code))
        ->assertRedirect(route('courses.show', $course->course_code));

    $this->actingAs($admin)->get(route('admin.courses.edit', $course))->assertOk()
        ->assertViewHas('course', fn (Course $shown): bool => $shown->ratings_count === 1
            && $shown->interestRating() === 5.0
            && $shown->difficultyPercentage() === 80
            && $shown->workloadPercentage() === 80)
        ->assertSee('0.00')->assertSee('100%');

    $rating = $secondUser->ratings()->firstOrFail();
    expect($rating->course->is($course))->toBeTrue()
        ->and($rating->user->is($secondUser))->toBeTrue();
});

test('admin statistics distinguish an empty course from a zero grade point average', function () {
    $course = Course::factory()->create(['course_code' => 'CP800002']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.courses.edit', $course))->assertOk()
        ->assertViewHas('course', fn (Course $shown): bool => $shown->ratings_count === 0
            && $shown->interestRating() === null
            && $shown->difficultyPercentage() === null)
        ->assertSee('ยังไม่มีข้อมูลจากผู้ใช้')
        ->assertDontSee('0.00');
});

test('renaming a course preserves its comments ratings views and one rating per user', function (bool $inline) {
    $course = Course::factory()->create(['course_code' => 'CP800003', 'views' => 7]);
    $user = User::factory()->create();
    $comment = $course->comments()->create(['user_id' => $user->id, 'content' => 'Keep this comment']);
    $rating = $course->ratings()->create([
        'user_id' => $user->id, 'difficulty' => 3, 'workload' => 4, 'interest' => 5, 'grade' => 'B+',
    ]);
    $this->actingAs(User::factory()->admin()->create());

    if ($inline) {
        $this->patchJson(route('admin.courses.inline-update', $course), ['course_code' => 'CP800004'])
            ->assertOk();
    } else {
        $this->put(route('admin.courses.update', $course), [
            'course_code' => 'CP800004',
            'course_name_en' => $course->course_name_en,
            'course_name_th' => $course->course_name_th,
            'major' => $course->major,
            'course_type' => $course->course_type,
            'credits' => $course->credits,
        ])->assertSessionHasNoErrors();
    }

    expect($course->fresh()->views)->toBe(7)
        ->and($comment->fresh()->course_code)->toBe('CP800004')
        ->and($rating->fresh()->course_code)->toBe('CP800004')
        ->and($rating->fresh()->course->is($course))->toBeTrue();

    $this->get(route('courses.show', 'CP800003'))->assertNotFound();
    $this->get(route('courses.show', 'CP800004'))->assertOk()->assertSee($comment->content);
    $this->actingAs($user)->post(route('courses.rating.store', 'CP800004'), [
        'difficulty' => 1, 'workload' => 1, 'interest' => 1,
    ])->assertSessionHas('rating_status', 'คุณให้คะแนนวิชานี้ไปแล้ว');
    $this->assertDatabaseCount('course_ratings', 1);
    expect($rating->fresh()->interest)->toBe(5);
})->with(['inline' => true, 'form' => false]);

test('invalid course relation ids self prerequisites and unsafe course codes are rejected', function () {
    $course = Course::factory()->create(['course_code' => 'CP800005']);
    $this->actingAs(User::factory()->admin()->create())
        ->patchJson(route('admin.courses.inline-update', $course), [
            'course_code' => 'CP/12345',
            'teacher_ids' => [99999],
            'topic_ids' => ['invalid'],
            'prerequisite_ids' => [$course->id],
        ])->assertUnprocessable()
        ->assertJsonValidationErrors(['course_code', 'teacher_ids.0', 'topic_ids.0', 'prerequisite_ids.0']);

    expect($course->fresh()->course_code)->toBe('CP800005')
        ->and($course->teachers()->count())->toBe(0)
        ->and($course->topics()->count())->toBe(0)
        ->and($course->prerequisiteCourses()->count())->toBe(0);
});

test('deleting a course removes only its related feedback and cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('courses/delete.jpg', 'cover');
    $course = Course::factory()->create(['course_code' => 'CP800006', 'image' => 'courses/delete.jpg']);
    $otherCourse = Course::factory()->create(['course_code' => 'CP800007']);
    $user = User::factory()->create();
    $comment = $course->comments()->create(['user_id' => $user->id, 'content' => 'Delete feedback']);
    $course->comments()->create(['user_id' => $user->id, 'content' => 'Reply', 'parent_id' => $comment->id]);
    $comment->likes()->attach($user);
    $comment->reports()->create(['user_id' => $user->id, 'reason' => 'spam']);
    $course->ratings()->create(['user_id' => $user->id, 'difficulty' => 3, 'workload' => 3, 'interest' => 4]);
    $keptComment = $otherCourse->comments()->create(['user_id' => $user->id, 'content' => 'Keep feedback']);

    $this->actingAs(User::factory()->admin()->create())->delete(route('admin.courses.destroy', $course))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    $this->assertDatabaseMissing('comments', ['course_code' => $course->course_code]);
    $this->assertDatabaseCount('course_ratings', 0);
    $this->assertDatabaseCount('comment_likes', 0);
    $this->assertDatabaseCount('reports', 0);
    expect($keptComment->fresh())->not->toBeNull();
    Storage::disk('public')->assertMissing('courses/delete.jpg');
});

test('courses referenced in study plans cannot be deleted along with their feedback or cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('courses/keep.jpg', 'cover');
    $course = Course::factory()->create(['course_code' => 'CP800008', 'image' => 'courses/keep.jpg']);
    $comment = $course->comments()->create(['user_id' => User::factory()->create()->id, 'content' => 'Keep this']);
    $plan = StudyPlan::query()->create([
        'major_code' => 'CS', 'name' => 'Plan', 'academic_year_start' => 2569,
    ]);
    $plan->courses()->create(['course_id' => $course->id, 'year' => 1, 'term' => 1, 'position' => 1]);

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('admin.courses.edit', $course))
        ->delete(route('admin.courses.destroy', $course))
        ->assertRedirect(route('admin.courses.edit', $course))
        ->assertSessionHasErrors('course');

    expect($course->fresh())->not->toBeNull()
        ->and($comment->fresh())->not->toBeNull();
    Storage::disk('public')->assertExists('courses/keep.jpg');
});

test('the report timestamp upgrade preserves existing data and can run on corrected schemas', function () {
    $course = Course::factory()->create(['course_code' => 'CP800009']);
    $user = User::factory()->create();
    $comment = $course->comments()->create(['user_id' => $user->id, 'content' => 'Reported comment']);
    $report = $comment->reports()->create([
        'user_id' => $user->id, 'reason' => 'spam', 'handled_at' => now()->startOfSecond(),
    ]);
    $handledAt = $report->handled_at;
    Schema::table('reports', fn (Blueprint $table) => $table->renameColumn('handled_at', 'handlet_at'));

    $migration = require database_path('migrations/2026_10_14_090000_fix_report_handled_at_column.php');
    $migration->up();
    $migration->up();

    expect(Schema::hasColumn('reports', 'handled_at'))->toBeTrue()
        ->and(Schema::hasColumn('reports', 'handlet_at'))->toBeFalse()
        ->and($report->fresh()->handled_at->equalTo($handledAt))->toBeTrue();
});
