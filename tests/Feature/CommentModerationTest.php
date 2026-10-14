<?php

use App\Models\BannedWord;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('blocked terms reject comments and review terms stay hidden until approved', function () {
    $course = Course::factory()->create(['course_code' => 'CP900001']);
    $user = User::factory()->create();
    BannedWord::query()->create(['word' => 'blockedterm', 'type' => 'block']);
    BannedWord::query()->create(['word' => 'reviewterm', 'type' => 'review']);
    Cache::forget('banned_words_block');
    Cache::forget('banned_words_review');

    $this->actingAs($user)->post(route('comments.store'), [
        'course_code' => $course->course_code,
        'content' => 'This contains blockedterm',
    ])->assertSessionHasErrors('content');

    $this->assertDatabaseCount('comments', 0);

    $this->actingAs($user)->post(route('comments.store'), [
        'course_code' => $course->course_code,
        'content' => 'This contains reviewterm',
    ])->assertSessionHasNoErrors()
        ->assertSessionHas('success');

    $pending = Comment::query()->firstOrFail();
    expect($pending->status)->toBe('pending');

    $this->get(route('courses.show', $course->course_code))
        ->assertOk()
        ->assertDontSee($pending->content)
        ->assertViewHas('course', fn (Course $shown): bool => $shown->comments_count === 0);

    $this->get(route('home'))
        ->assertOk()
        ->assertViewHas('reviews', fn ($reviews): bool => $reviews->isEmpty());

    $this->actingAs($user)->post(route('comments.like', $pending))
        ->assertNotFound();
});

test('ordinary comments are approved and shown on the course page', function () {
    $course = Course::factory()->create(['course_code' => 'CP900002']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('comments.store'), [
        'course_code' => $course->course_code,
        'content' => 'A useful course review',
    ])->assertSessionHasNoErrors();

    $comment = Comment::query()->firstOrFail();
    expect($comment->status)->toBe('approved');

    $this->get(route('courses.show', $course->course_code))
        ->assertOk()
        ->assertSee($comment->content)
        ->assertViewHas('course', fn (Course $shown): bool => $shown->comments_count === 1);
});

test('admin approval and removal update public comments and course statistics', function () {
    $course = Course::factory()->create(['course_code' => 'CP900003']);
    $comment = $course->comments()->create([
        'user_id' => User::factory()->create()->id,
        'content' => 'A comment awaiting moderation',
        'status' => Comment::STATUS_PENDING,
    ]);
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('admin.moderation.index'))->assertOk()->assertSee($comment->content);
    $this->patch(route('admin.moderation.approve', $comment))->assertSessionHasNoErrors();

    $this->get(route('courses.show', $course->course_code))->assertOk()
        ->assertSee($comment->content)
        ->assertViewHas('course', fn (Course $shown): bool => $shown->comments_count === 1);
    $this->get(route('home'))->assertOk()
        ->assertViewHas('reviews', fn ($reviews): bool => $reviews->contains('id', $comment->id));
    $this->get(route('admin.courses.edit', $course))->assertOk()
        ->assertViewHas('course', fn (Course $shown): bool => $shown->comments_count === 1);

    $this->delete(route('admin.moderation.destroy', $comment))->assertSessionHasNoErrors();

    expect($comment->fresh()->status)->toBe('deleted');
    $this->get(route('courses.show', $course->course_code))->assertOk()
        ->assertDontSee($comment->content)
        ->assertViewHas('course', fn (Course $shown): bool => $shown->comments_count === 0);
    $this->get(route('home'))->assertOk()
        ->assertViewHas('reviews', fn ($reviews): bool => $reviews->isEmpty());
    $this->get(route('admin.courses.edit', $course))->assertOk()
        ->assertSee($comment->content)
        ->assertViewHas('course', fn (Course $shown): bool => $shown->comments_count === 0);
});

test('moderation and report actions are restricted to admins', function () {
    $course = Course::factory()->create(['course_code' => 'CP900005']);
    $owner = User::factory()->create();
    $reporter = User::factory()->create();
    $comment = $course->comments()->create([
        'user_id' => $owner->id, 'content' => 'Unchanged comment', 'status' => Comment::STATUS_PENDING,
    ]);
    $report = $comment->reports()->create(['user_id' => $reporter->id, 'reason' => 'spam']);

    $this->get(route('admin.moderation.index'))->assertRedirect(route('auth.login'));
    $this->patch(route('admin.moderation.approve', $comment))->assertRedirect(route('auth.login'));
    $this->patch(route('admin.reports.remove-comment', $report))->assertRedirect(route('auth.login'));

    $this->actingAs($reporter)->get(route('admin.reports.index'))->assertRedirect(route('home'));
    $this->patch(route('admin.moderation.approve', $comment))->assertRedirect(route('home'));
    $this->delete(route('admin.moderation.destroy', $comment))->assertRedirect(route('home'));
    $this->patch(route('admin.reports.dismiss', $report))->assertRedirect(route('home'));
    $this->patch(route('admin.reports.remove-comment', $report))->assertRedirect(route('home'));
    $this->patchJson(route('admin.reports.remove-comment', $report))->assertForbidden();

    expect($comment->fresh()->status)->toBe(Comment::STATUS_PENDING)
        ->and($report->fresh()->status)->toBe(Report::STATUS_PENDING)
        ->and($report->fresh()->handled_at)->toBeNull();
});
