<?php

use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseRating;
use App\Models\StudyPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('empty catalogs do not invent database records and static study plans remain available', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('ยังไม่มีรายวิชาที่เผยแพร่')
        ->assertSee('ยังไม่มีผู้ให้คะแนน')
        ->assertSee('ยังไม่มีความคิดเห็นจากผู้เรียน')
        ->assertSee('ตัวอย่างการแสดงผล')
        ->assertViewHas('popularCourses', fn ($courses): bool => $courses->isEmpty());

    $this->assertDatabaseCount('courses', 0);

    $this->get(route('plans.index'))
        ->assertOk()
        ->assertSee('data-academic-year="2565"', false)
        ->assertSee('ภาษาอังกฤษ 1')
        ->assertSee('CP 499 001')
        ->assertDontSee('ยังไม่มีแผนการเรียนที่เผยแพร่');

    $this->assertDatabaseCount('study_plans', 0);
    $this->assertDatabaseCount('study_plan_courses', 0);

    $this->get(route('courses.show', 'CP422301'))->assertNotFound();
});

test('study plan navigation shows every supported major and all four years without creating data', function () {
    $response = $this->get(route('plans.index'))->assertOk();

    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
    $xpath = new DOMXPath($document);

    expect($xpath->query('//*[@data-major-grid]/button'))->toHaveCount(5);

    foreach (array_keys(Course::MAJORS) as $majorCode) {
        $details = '//*[@data-plan-detail and @data-major-key="'.$majorCode.'"]';

        expect($xpath->query($details))->toHaveCount(1);

        foreach (range(1, 4) as $year) {
            expect($xpath->query($details.'//*[@data-plan-year="'.$year.'"]'))->toHaveCount(1)
                ->and($xpath->query($details.'//*[@data-plan-year-panel="'.$year.'"]//table'))->toHaveCount(2);
        }
    }

    $response->assertSee(asset('images/photo-5.jpg'), false)
        ->assertSee(asset('images/photo-4.jpg'), false)
        ->assertSee(asset('images/photo-3.jpg'), false)
        ->assertSee(asset('images/photo-1.jpg'), false)
        ->assertSee(asset('images/photo-2.jpg'), false);

    $this->assertDatabaseCount('courses', 0);
    $this->assertDatabaseCount('study_plans', 0);
    $this->assertDatabaseCount('study_plan_courses', 0);
});

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

test('static study plan content is independent of stored courses and curricula', function () {
    $course = Course::factory()->create(['course_code' => 'CP999010']);
    $plan = StudyPlan::query()->create([
        'major_code' => 'CS',
        'name' => 'หลักสูตรจากฐานข้อมูล',
        'academic_year_start' => 2569,
        'is_published' => true,
    ]);
    $plan->courses()->create([
        'course_id' => $course->id,
        'year' => 4,
        'term' => 2,
        'position' => 1,
    ]);

    $this->get(route('plans.index'))
        ->assertOk()
        ->assertSee('data-academic-year="2565"', false)
        ->assertSee('CP352220')
        ->assertSee('CP 499 101')
        ->assertDontSee('หลักสูตรจากฐานข้อมูล')
        ->assertDontSee('CP999010');

    $this->assertDatabaseCount('courses', 1);
    $this->assertDatabaseCount('study_plans', 1);
    $this->assertDatabaseCount('study_plan_courses', 1);
});

test('each static study plan renders the original courses and credits across all eight terms', function () {
    $response = $this->get(route('plans.index'))->assertOk();

    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
    $xpath = new DOMXPath($document);
    $termRows = [1 => [6, 7], 2 => [5, 5], 3 => [4, 4], 4 => [4, 1]];
    $majorCourses = [
        'ITII' => 'CP410201',
        'CS' => 'CP352220',
        'GIS' => 'GI310101',
        'AI' => 'CP354101',
        'CY' => 'CP422301',
    ];

    foreach ($majorCourses as $majorCode => $firstCourseCode) {
        $detail = '//*[@data-plan-detail and @data-major-key="'.$majorCode.'"]';

        foreach ($termRows as $year => $courseCounts) {
            foreach ($courseCounts as $termIndex => $courseCount) {
                $term = $detail.'//*[@data-plan-year-panel="'.$year.'"]//article['.($termIndex + 1).']';

                expect($xpath->query($term.'//tbody/tr'))->toHaveCount($courseCount + 2);
            }
        }

        $firstYear = $detail.'//*[@data-plan-year-panel="1"]';
        $secondYear = $detail.'//*[@data-plan-year-panel="2"]';
        $fourthYear = $detail.'//*[@data-plan-year-panel="4"]';

        expect(trim($xpath->query($firstYear.'//article[1]//tbody/tr[1]/td[1]')->item(0)->textContent))->toBe('LI 101 101')
            ->and(trim($xpath->query($firstYear.'//article[1]//tbody/tr[1]/td[3]')->item(0)->textContent))->toBe('3(3-0-6)')
            ->and(trim($xpath->query($firstYear.'//article[1]//tbody/tr[7]/td[2]')->item(0)->textContent))->toBe('18')
            ->and(trim($xpath->query($firstYear.'//article[2]//tbody/tr[9]/td[2]')->item(0)->textContent))->toBe('39')
            ->and(trim($xpath->query($secondYear.'//article[1]//tbody/tr[1]/td[1]')->item(0)->textContent))->toBe($firstCourseCode)
            ->and(trim($xpath->query($fourthYear.'//article[1]//tbody/tr[1]/td[4]')->item(0)->textContent))->toBe('CP 499 001')
            ->and(trim($xpath->query($fourthYear.'//article[2]//tbody/tr[1]/td[1]')->item(0)->textContent))->toBe('CP 499 101')
            ->and(trim($xpath->query($fourthYear.'//article[2]//tbody/tr[2]/td[2]')->item(0)->textContent))->toBe('6')
            ->and(trim($xpath->query($fourthYear.'//article[2]//tbody/tr[3]/td[2]')->item(0)->textContent))->toBe('111');
    }
});

test('comments require a published course and a parent on the same course', function () {
    $user = User::factory()->create();
    Course::factory()->create(['course_code' => 'CP999005']);
    Course::factory()->create(['course_code' => 'CP999006']);
    Course::factory()->draft()->create(['course_code' => 'CP999007']);
    $otherCourseComment = Comment::query()->create([
        'user_id' => $user->id,
        'course_code' => 'CP999006',
        'content' => 'ความคิดเห็นเดิม',
    ]);

    $this->actingAs($user)->post(route('comments.store'), [
        'course_code' => 'CP999005',
        'content' => 'ตอบกลับผิดวิชา',
        'parent_id' => $otherCourseComment->id,
    ])->assertSessionHasErrors('parent_id');

    $this->post(route('comments.store'), [
        'course_code' => 'CP999007',
        'content' => 'วิชาที่ซ่อนอยู่',
    ])->assertSessionHasErrors('course_code');

    $this->post(route('comments.store'), [
        'course_code' => ['CP999005'],
        'content' => 'รหัสวิชาผิดรูปแบบ',
    ])->assertSessionHasErrors('course_code');

    $this->post(route('comments.store'), [
        'course_code' => 'CP999005',
        'content' => 'ความคิดเห็นจริง',
    ])->assertSessionHasNoErrors();

    $this->assertDatabaseCount('comments', 2);
});

test('comment validation rejects malformed references and ignores unvalidated ownership', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    Course::factory()->create(['course_code' => 'CP999009']);

    $this->actingAs($author)->post(route('comments.store'), [
        'course_code' => ['CP999009'],
        'content' => 'Invalid course',
        'parent_id' => ['1'],
    ])->assertSessionHasErrors(['course_code', 'parent_id']);

    $this->post(route('comments.store'), [
        'course_code' => 'CP999009',
        'content' => 'Valid comment',
        'user_id' => $otherUser->id,
    ])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('comments', [
        'course_code' => 'CP999009',
        'content' => 'Valid comment',
        'user_id' => $author->id,
    ]);
    $this->assertDatabaseCount('comments', 1);
});
