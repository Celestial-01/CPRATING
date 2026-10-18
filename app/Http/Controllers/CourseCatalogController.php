<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CourseCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $searchTerm = trim($validated['q'] ?? '');
        $majorCodes = array_keys(Course::MAJORS);
        $requestedFilter = $request->query('category', 'all');
        $selectedFilter = is_string($requestedFilter)
            && in_array($requestedFilter, ['all', 'popular', ...$majorCodes], true)
            ? $requestedFilter
            : 'all';
        $courses = Course::query()
            ->forPublicCatalog()
            ->matching($searchTerm)
            ->when(
                in_array($selectedFilter, $majorCodes, true),
                fn ($query) => $query->where('major', $selectedFilter),
            )
            ->when($selectedFilter === 'popular', fn ($query) => $query->popular())
            ->orderBy('course_code')
            ->paginate((int) config('courses.per_page', 8))
            ->withQueryString();
        $query = $searchTerm;
        $courseCopy = config('courses.copy', []);

        return view('courses.index', compact(
            'courses',
            'majorCodes',
            'query',
            'selectedFilter',
            'courseCopy',
        ));
    }

    public function show(Request $request, Course $course): View
    {
        $course = Course::query()->forPublicCatalog()->findOrFail($course->getKey());

        if (! $request->boolean('loading') && $request->isMethod('get')) {
            $course->recordView();
        }

        $sort = $request->query('sort') === 'popular' ? 'popular' : 'latest';
        $viewerId = $request->user()?->id;
        $likedByViewer = fn ($query) => $query->where('users.id', $viewerId);

        $comments = $course->comments()
            ->approved()
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies' => fn ($query) => $query
                    ->approved()
                    ->with(['user', 'replyToUser'])
                    ->withCount('likes')
                    ->withExists(['likes as liked_by_me' => $likedByViewer]),
            ])
            ->withCount('likes')
            ->withExists(['likes as liked_by_me' => $likedByViewer])
            ->when(
                $sort === 'popular',
                fn ($query) => $query->orderByDesc('likes_count')->latest(),
                fn ($query) => $query->latest(),
            )
            ->get();

        $hasRated = $request->user() !== null && $course->ratings()
            ->where('user_id', $viewerId)
            ->exists();
        $gradeCounts = $course->reportedGradeCounts();
        $gradeSampleSize = $gradeCounts->sum();
        $averageGradePoint = $course->averageGradePoint($gradeCounts);
        $relatedCourses = Course::query()
            ->forPublicCatalog()
            ->where('major', $course->major)
            ->where('course_code', '!=', $course->course_code)
            ->orderBy('course_code')
            ->limit(4)
            ->get();
        $courseDetailCopy = config('courses.detail.copy', []);
        $showSkeleton = $request->boolean('loading');

        return view('courses.show', compact(
            'course',
            'hasRated',
            'gradeCounts',
            'gradeSampleSize',
            'averageGradePoint',
            'relatedCourses',
            'comments',
            'courseDetailCopy',
            'showSkeleton',
        ));
    }
    public function trashed(): View
    {
        $courses = Course::onlyTrashed()
            ->orderBy('course_code')
            ->get();

        return view('admin.courses.trashed', compact('courses'));
    }

    public function restore(int $id): RedirectResponse
    {
        $course = Course::onlyTrashed()->findOrFail($id);

        if ($course->studyPlanCourses()->exists()) {
            throw ValidationException::withMessages([
                'course' => 'ไม่สามารถกู้คืนรายวิชาได้ เนื่องจากมีรายการในแผนการเรียน',
            ]);
        }

        $course->restore();

        return to_route('admin.courses.trashed')
            ->with('success', 'กู้คืนรายวิชาเรียบร้อยแล้ว');
    }
    }
