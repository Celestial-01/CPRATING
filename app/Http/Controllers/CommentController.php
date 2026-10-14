<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Rules\NoBannedWords;
use App\Services\ContentModerator;
use App\Models\Report;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /**
     * บันทึกคอมเมนต์
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:50'],
            'content'     => ['required', 'string', 'max:2000', new NoBannedWords],
            'parent_id'   => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $parentId = null;
        $replyToUserId = null;

        if (!empty($validated['parent_id'])) {
            $target = Comment::findOrFail($validated['parent_id']);

            // ถ้ากดตอบกลับ "รีพลาย" ให้ไปอยู่ใต้คอมเมนต์หลักเสมอ (แสดงแค่ 1 ชั้นแบบ Instagram)
            $parentId = $target->parent_id ?? $target->id;
            $replyToUserId = $target->user_id;
        }

        $needsReview = ContentModerator::needsReview($validated['content']);
        
        Comment::create([
            'user_id'          => $request->user()->id,
            'course_code'      => $validated['course_code'],
            'content'          => $validated['content'],
            'status'           => $needsReview ? 'pending' : 'approved',
            'parent_id'        => $parentId,
            'reply_to_user_id' => $replyToUserId,
        ]);

        return back()->with('success', $needsReview
            ? 'ความคิดเห็นของคุณอยู่ระหว่างรอการตรวจสอบ'
            : 'เพิ่มความคิดเห็นเรียบร้อยแล้ว');
    }

    /**
     * ลบคอมเมนต์ของตัวเอง
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        abort_unless((int) $comment->user_id === (int) auth()->id(), 403);

        $comment->replies()->delete();
        $comment->delete();

        return back()->with('success', 'ลบความคิดเห็นเรียบร้อยแล้ว');
    }
    //กดใจคอมเม้นต์
    public function toggleLike(Request $request, Comment $comment): JsonResponse
    {
    $result = $comment->likes()->toggle($request->user()->id);

    return response()->json([
        'liked' => count($result['attached']) > 0,
        'count' => $comment->likes()->count(),
    ]);
    }

    public function report(Request $request, Comment $comment): RedirectResponse
    {
        //ไม่สามารถรีพอตคอมเมนต์ตัวเองได้
        abort_if((int) $comment->user_id === (int) $request->user()->id, 403);
        abort_unless($comment->status === 'approved', 404);

        $validated = $request->validate([
            'reason'  => ['required', Rule::in(['spam', 'offensive', 'irrelevant', 'other'])],
            'details' => ['nullable', 'string', 'max:500'],
        ]);

        // รีพอตซ้ำจะไม่ถูกนับเพิ่ม
        Report::firstOrCreate(
            ['comment_id' => $comment->id, 'user_id' => $request->user()->id],
            ['reason' => $validated['reason'], 'details' => $validated['details'] ?? null]
        );

        return back()->with('success', 'ขอบคุณสำหรับการรายงาน เจ้าหน้าที่กำลังดำเนินการตรวจสอบ');
    }
}
