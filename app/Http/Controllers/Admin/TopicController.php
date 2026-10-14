<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TopicController extends Controller
{
    public function index(): View
    {
        $topics = Topic::query()->orderBy('name')->get();

        return view('admin.topics.index', compact('topics'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('topics', 'name')],
        ]);

        Topic::create($validated);

        return to_route('admin.topics.index')->with('success', 'เพิ่มหัวข้อเรียบร้อยแล้ว');
    }

    public function update(Request $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics', 'name')->ignore($topic),
            ],
        ]);

        $topic->update($validated);

        return to_route('admin.topics.index')->with('success', 'แก้ไขหัวข้อเรียบร้อยแล้ว');
    }

    public function destroy(Topic $topic): RedirectResponse
    {
        $topic->delete();

        return to_route('admin.topics.index')->with('success', 'ลบหัวข้อเรียบร้อยแล้ว');
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['keyword' => ['nullable', 'string', 'max:100']]);
        $keyword = trim((string) $request->input('keyword', ''));

        return response()->json(
            Topic::query()
                ->where('name', 'like', "%{$keyword}%")
                ->limit(10)
                ->get(),
        );
    }
}
