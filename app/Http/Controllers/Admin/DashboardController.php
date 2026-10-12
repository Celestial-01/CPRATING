<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $majors = $this->majorOptions();
        $types = $this->courseTypes();
        $currentMajor = (string) $request->query('major', '');

        if (! array_key_exists($currentMajor, $majors)) {
            $currentMajor = (string) array_key_first($majors);
        }

        $currentTypes = array_values(array_intersect(
            (array) $request->query('types', []),
            $types,
        ));
        $keyword = trim((string) $request->query('q', ''));

        $courses = Course::query()
            ->where('major', $currentMajor)
            ->when(
                $currentTypes !== [],
                fn (Builder $query): Builder => $query->whereIn('course_type', $currentTypes),
            )
            ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query
                        ->where('course_name_th', 'like', "%{$keyword}%")
                        ->orWhere('course_name_en', 'like', "%{$keyword}%")
                        ->orWhere('course_code', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('course_code')
            ->get();

        return view('admin.dashboard', compact(
            'courses',
            'majors',
            'types',
            'currentMajor',
            'currentTypes',
            'keyword',
        ));
    }

    /**
     * Keep the partner's default program choices, while also exposing catalog
     * values already stored by the current application.
     *
     * @return array<string, string>
     */
    private function majorOptions(): array
    {
        $storedMajors = Course::query()
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major', 'major')
            ->all();

        return $storedMajors !== []
            ? $storedMajors + Course::MAJORS
            : Course::MAJORS;
    }

    /**
     * @return list<string>
     */
    private function courseTypes(): array
    {
        $storedTypes = Course::query()
            ->whereNotNull('course_type')
            ->where('course_type', '!=', '')
            ->distinct()
            ->orderBy('course_type')
            ->pluck('course_type')
            ->all();

        return array_values(array_unique([...$storedTypes, ...Course::TYPES]));
    }
}
