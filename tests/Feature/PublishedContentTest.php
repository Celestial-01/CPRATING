<?php

use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseRating;
use App\Models\StudyPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('published course data and rating averages come from database records', function () {
    $course = Course::factory()->create([
        'course_code' => 'CP999001',
        'course_name_th' => 'รายวิชาทดสอบ',
    ]);
    Course::factory()->draft()->create(['course_code' => 'CP999002']);
    $user = User::factory()->create();

    CourseRating::query()->create([
        'user_id' => $user->id,
        'course_code' => $course->course_code,
        'difficulty' => 4,
        'workload' => 3,
        'interest' => 5,
    ]);

    $this->get(route('courses.index'))
        ->assertOk()
        ->assertSee('class="course-card"', false)
        ->assertSee('CP999001')
        ->assertDontSee('CP999002');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('class="hero-float-card"', false)
        ->assertSee('รายวิชาทดสอบ')
        ->assertSee('จาก 1 คะแนน');

    $this->get(route('courses.show', 'CP999002'))->assertNotFound();
});

test('home review showcase stays illustrative while real comments appear in the highlight', function () {
    $course = Course::factory()->create(['course_code' => 'CP999008']);
    Comment::query()->create([
        'user_id' => User::factory()->create()->id,
        'course_code' => $course->course_code,
        'content' => 'รีวิวจากฐานข้อมูลจริง',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('รีวิวจากฐานข้อมูลจริง')
        ->assertSee('ตัวอย่างการแสดงผล')
        ->assertSee('ตัวอย่างคะแนนความน่าสนใจ 4.8 จาก 5');
});

test('the published study plan model only includes published courses', function () {
    $publishedCourse = Course::factory()->create(['course_code' => 'CP999003']);
    $draftCourse = Course::factory()->draft()->create(['course_code' => 'CP999004']);
    $plan = StudyPlan::query()->create([
        'major_code' => 'CS',
        'name' => 'วิทยาการคอมพิวเตอร์',
        'academic_year_start' => 2569,
        'is_published' => true,
    ]);
    $plan->courses()->create(['course_id' => $publishedCourse->id, 'year' => 1, 'term' => 2, 'position' => 1]);
    $plan->courses()->create(['course_id' => $draftCourse->id, 'year' => 1, 'term' => 2, 'position' => 2]);
    $unpublishedPlan = StudyPlan::query()->create([
        'major_code' => 'AI',
        'name' => 'หลักสูตรที่ยังไม่เผยแพร่',
        'academic_year_start' => 2569,
        'is_published' => false,
    ]);
    $unpublishedPlan->courses()->create(['course_id' => $publishedCourse->id, 'year' => 1, 'term' => 1, 'position' => 1]);

    $publishedPlan = StudyPlan::latestPublishedPerMajor()->first();
    expect($publishedPlan->courses)->toHaveCount(1)
        ->and($publishedPlan->courses->first()->course->course_code)->toBe('CP999003')
        ->and($publishedPlan->termCredits(1, 2))->toBe($publishedCourse->credits)
        ->and($publishedPlan->accumulatedCredits(1, 2))->toBe($publishedCourse->credits);

    expect(StudyPlan::latestPublishedPerMajor())->toHaveCount(1);
});
