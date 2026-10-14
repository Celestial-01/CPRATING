<?php

namespace App\Http\Controllers;

use App\Support\CourseCatalog;
use Illuminate\Contracts\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $courseCatalog = CourseCatalog::all();

        return view('history.index', [
            'courses' => $courseCatalog,
            'historyCopy' => config('history.copy', []),
        ]);
    }
}
