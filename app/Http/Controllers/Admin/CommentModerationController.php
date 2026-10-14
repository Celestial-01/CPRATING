<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommentModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $comments = Comment::with('user')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return view('admin.moderation.index', compact('comments', 'status'));
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'approved']);

        return back()->with('success', 'อนุมัติความคิดเห็นแล้ว');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'deleted']);

        return back()->with('success', 'ลบความคิดเห็นแล้ว');
    }
}
