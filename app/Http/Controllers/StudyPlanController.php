<?php

namespace App\Http\Controllers;

use App\Support\StudyPlanCatalog;
use Illuminate\Contracts\View\View;

class StudyPlanController extends Controller
{
    public function index(): View
    {
        $majors = StudyPlanCatalog::majors();

        return view('plans.index', [
            'majors' => $majors,
            'years' => StudyPlanCatalog::yearOptions(),
            'academicYearStart' => config('study-plans.academic_year_start'),
            'planCopy' => config('study-plans.copy', []),
            'plans' => $majors->mapWithKeys(function (array $major) use ($majors): array {
                return [$major['key'] => StudyPlanCatalog::years($major['key'], $majors)];
            })->all(),
        ]);
    }
}
