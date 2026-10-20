<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $viewedCourses = $request->user()->viewedCourses()
            ->forPublicCatalog()
            ->orderByDesc('user_course_views.viewed_at')
            ->get();
        $savedCourses = $request->user()->savedCourses()
            ->forPublicCatalog()
            ->orderByDesc('user_saved_courses.created_at')
            ->get();
        $historyCopy = config('history.copy', []);

        return view('history.index', compact('viewedCourses', 'savedCourses', 'historyCopy'));
    }
}
