<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function showDashboard(Request $request): View
    {
        $majors = Course::MAJORS;
        $types  = Course::TYPES;

        $currentMajor = $request->query('major');
        if (! array_key_exists($currentMajor, $majors)) {
            $currentMajor = array_key_first($majors); //major default
        }
        $currentTypes = array_values(array_intersect((array) $request->query('types', []), $types)); //pull type 

        $keyword = trim((string) $request->query('q', '')); //search

        $courses = Course::where('major', $currentMajor)
            ->when($currentTypes, fn ($query) => $query->whereIn('course_type', $currentTypes))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('course_name_th', 'like', "%{$keyword}%")
                        ->orWhere('course_name_en', 'like', "%{$keyword}%")
                        ->orWhere('course_code', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('course_code')
            ->get();

        return view('admin.dashboard', compact(
            'courses', 'majors', 'types', 'currentMajor', 'currentTypes', 'keyword'
        ));
    }
}