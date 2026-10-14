<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseRating;
use App\Models\StudyPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $heroCourses = Course::query()
            ->forPublicCatalog()
            ->orderByDesc('ratings_count')
            ->orderBy('course_code')
            ->limit(8)
            ->get();
        $popularCourses = Course::query()
            ->forPublicCatalog()
            ->orderByDesc('ratings_count')
            ->orderByDesc('interest_average')
            ->orderBy('course_code')
            ->limit(5)
            ->get();
        $reviews = Comment::query()
            ->approved()
            ->whereNull('parent_id')
            ->whereHas('course', fn ($query) => $query->published())
            ->with('course')
            ->latest()
            ->take(3)
            ->get();
        $highlight = CourseRating::query()
            ->selectRaw('COUNT(*) as rating_count, AVG(interest) as rating, AVG(workload) as workload, AVG(difficulty) as difficulty')
            ->whereHas('course', fn ($query) => $query->published())
            ->first();
        $curriculumPlan = StudyPlan::latestPublishedPerMajor()->first();
        $homeCopy = config('home.copy', []);
        $showSkeleton = $request->boolean('loading');

        return view('home', compact(
            'showSkeleton',
            'heroCourses',
            'popularCourses',
            'reviews',
            'highlight',
            'curriculumPlan',
            'homeCopy',
        ));
    }
}
