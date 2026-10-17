<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        return to_route('admin.topics.index')
            ->with('success', 'เพิ่มหัวข้อเรียบร้อยแล้ว');
    }

    public function update(Request $request,Topic $topic): RedirectResponse|JsonResponse
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

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'topic' => [
                    'id' => $topic->id,
                    'name' => $topic->name,
                ],
            ]);
        }

        return to_route('admin.topics.index')
            ->with('success', 'แก้ไขหัวข้อเรียบร้อยแล้ว');
        }

    public function destroy(Topic $topic): RedirectResponse
    {
        $topic->delete();

        return to_route('admin.topics.index')
            ->with('success', 'ลบหัวข้อเรียบร้อยแล้ว');
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
        ]);

        $keyword = trim((string) $request->input('keyword', ''));

        return response()->json(
            Topic::query()
                ->where('name', 'like', "%{$keyword}%")
                ->orderBy('name')
                ->limit(10)
                ->get(),
        );
    }

    public function trashed(): View
    {
        $topics = Topic::onlyTrashed()
            ->orderBy('name')
            ->get();

        return view('admin.topics.trashed', compact('topics'));
    }

    public function restore(int $id): RedirectResponse
    {
        $topic = Topic::onlyTrashed()->findOrFail($id);

        if (Topic::withTrashed()
            ->where('name', $topic->name)
            ->where('id', '!=', $topic->id)
            ->exists()) {
            throw ValidationException::withMessages([
                'name' => 'ไม่สามารถกู้คืนได้ เพราะมีหัวข้อชื่อนี้อยู่แล้ว',
            ]);
        }

        $topic->restore();

        return to_route('admin.topics.trashed')
            ->with('success', 'กู้คืนหัวข้อเรียบร้อยแล้ว');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $topic = Topic::onlyTrashed()->findOrFail($id);

        $topic->forceDelete();

        return to_route('admin.topics.trashed')
            ->with('success', 'ลบหัวข้อถาวรเรียบร้อยแล้ว');
    }
}
