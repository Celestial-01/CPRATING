<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CourseRating;
use App\Support\CourseCatalog;
use App\Support\CourseGradeDistribution;
use App\Support\CourseOverviewStats;
use App\Support\CourseRatingSummary;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourseCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $courseCatalog = CourseCatalog::all();
        $searchTerm = trim((string) $request->query('q', ''));
        $majorCodes = config('courses.major_codes', []);
        $requestedFilter = $request->query('category', 'all');
        $selectedFilter = is_string($requestedFilter)
            && in_array($requestedFilter, ['all', 'popular', ...$majorCodes], true)
            ? $requestedFilter
            : 'all';

        return view('courses.index', [
            'courses' => $courseCatalog,
            'majorCodes' => $majorCodes,
            'query' => $searchTerm,
            'selectedFilter' => $selectedFilter,
            'perPage' => config('courses.per_page', 8),
            'courseCopy' => config('courses.copy', []),
        ]);
    }

    public function show(Request $request, string $code): View
    {
        $courseCatalog = CourseCatalog::all();
        $course = CourseCatalog::find($code, $courseCatalog);

        if ($course === null) {
            throw new NotFoundHttpException;
        }

        $sort = $request->query('sort') === 'popular' ? 'popular' : 'latest';

        $comments = Comment::approved()
            ->where('course_code' , $code)
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies' => fn ($q) => $q
                ->approved()
                    ->with(['user', 'replyToUser'])
                    ->withCount('likes')
                    ->withExists(['likes as liked_by_me' => fn ($l) => $l->where('users.id', auth()->id())]),
            ])
            ->withCount('likes')
            ->withExists(['likes as liked_by_me' => fn ($l) => $l->where('users.id', auth()->id())])
            ->when(
                $sort === 'popular',
                fn ($q) => $q->orderByDesc('likes_count')->latest(), // ยอดกดใจมากสุดก่อน เท่ากันให้ใหม่กว่าขึ้นก่อน
                fn ($q) => $q->latest()
            )
            ->get();

        $ratingSummary = CourseRatingSummary::forCourse($course['code']);
        $course['rating'] = $ratingSummary['rating'];
        $course['difficulty'] = $ratingSummary['difficulty'];
        $course['workload'] = $ratingSummary['workload'];
        $course['rating_count'] = $ratingSummary['count'];

        $hasRated = $request->user() !== null && CourseRating::query()
            ->where('course_code', $course['code'])
            ->where('user_id', $request->user()->id)
            ->exists();

        return view('courses.show', [
            'course' => $course,
            'hasRated' => $hasRated,
            'relatedCourses' => $courseCatalog
                ->where('code', '!=', $course['code'])
                ->take(4)
                ->values()
                ->all(),
            'courseOverviewStats' => CourseOverviewStats::forCourse($course),
            'gradeDistribution' => CourseGradeDistribution::forCourse($course),
            'comments' => $comments,
            'courseDetailCopy' => config('courses.detail.copy', []),
            'showSkeleton' => $request->boolean('loading'),
        ]);
    }
}