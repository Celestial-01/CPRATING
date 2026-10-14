<?php

use App\Models\Course;
use App\Models\StudyPlan;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

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
