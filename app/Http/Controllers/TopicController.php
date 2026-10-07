<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        $topics = Topic::all();

        return view('topics.index', compact('topics'));
    }

    public function create(): View
    {
        return view('topics.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:topics,name'],
        ]);

        Topic::create($validated);

        return redirect()
            ->route('topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function show(Topic $topic): View
    {
        $topic->load('courses');

        return view('topics.show', compact('topic'));
    }

    public function edit(Topic $topic): View
    {
        return view('topics.edit', compact('topic'));
    }

    public function update(Request $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:topics,name,' . $topic->id,
            ],
        ]);

        $topic->update($validated);

        return redirect()
            ->route('topics.index')
            ->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic): RedirectResponse
    {
        $topic->delete();

        return redirect()
            ->route('topics.index')
            ->with('success', 'Topic deleted successfully.');
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = $request->input('keyword', '');

        $topics = Topic::where('name', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        return response()->json($topics);
    }
}