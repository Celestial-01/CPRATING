<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class StudyPlanController extends Controller
{
    public function index(): View
    {
        $curriculum = File::json(resource_path('data/study-plans.json'));
        $academicYearStart = $curriculum['academic_year_start'];
        $years = $curriculum['years'];
        $studyPlans = collect($curriculum['majors'])->map(function (array $major) use ($curriculum, $years): array {
            $yearPlans = $curriculum['common_years'] + [2 => $major['second_year']];
            $accumulatedCredits = 0;
            $yearTerms = [];

            foreach ($years as $year) {
                foreach ($yearPlans[$year] as $termIndex => $courses) {
                    $registeredCredits = collect($courses)->sum(fn (array $course): int => (int) $course['credits']);
                    $accumulatedCredits += $registeredCredits;

                    $yearTerms[$year][$termIndex + 1] = [
                        'courses' => $courses,
                        'registered_credits' => $registeredCredits,
                        'accumulated_credits' => $accumulatedCredits,
                    ];
                }
            }

            return [
                'name' => $major['name'],
                'image' => $major['image'],
                'years' => $yearTerms,
            ];
        });

        return view('plans.index', compact('academicYearStart', 'years', 'studyPlans'));
    }
}
