<?php

namespace App\Http\Controllers;

use App\Support\CourseCatalog;
use App\Support\HomeHeroCards;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $courses = CourseCatalog::all();
        $heroCourses = CourseCatalog::featured($courses);

        return view('home', [
            'showSkeleton' => $request->boolean('loading'),
            'heroCards' => HomeHeroCards::forCourses($heroCourses),
            'popularCourses' => CourseCatalog::featuredPopular($courses),
            'reviews' => config('home.reviews', []),
            'curriculumPreview' => config('home.curriculum_preview', []),
            'homeCopy' => config('home.copy', []),
        ]);
    }
}
