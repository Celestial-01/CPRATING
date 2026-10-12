<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * บันทึกคอมเมนต์
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        Comment::create([
            'user_id' => $request->user()->id,
            'course_code' => $validated['course_code'],
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'เพิ่มความคิดเห็นเรียบร้อยแล้ว');
    }

    /**
     * ลบคอมเมนต์ของตัวเอง
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        abort_unless(
            (int) $comment->user_id === (int) auth()->id(),
            403
        );

        $comment->delete();

        return back()->with('success', 'ลบความคิดเห็นเรียบร้อยแล้ว');
    }
}
