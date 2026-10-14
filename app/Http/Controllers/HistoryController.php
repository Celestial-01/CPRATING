<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Contracts\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()
            ->forPublicCatalog()
            ->orderBy('course_code')
            ->get();
        $historyCopy = config('history.copy', []);

        return view('history.index', compact('courses', 'historyCopy'));
    }
}
