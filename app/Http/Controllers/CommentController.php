<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Report;
use App\Rules\NoBannedWords;
use App\Services\ContentModerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $courseCode = $request->input('course_code');
        $courseCode = is_string($courseCode) ? $courseCode : '';

        $validated = $request->validate([
            'course_code' => [
                'bail',
                'required',
                'string',
                'max:50',
                Rule::exists('courses', 'course_code')
                    ->where('publication_status', Course::PUBLICATION_PUBLISHED),
            ],
            'content' => ['bail', 'required', 'string', 'max:2000', new NoBannedWords],
            'parent_id' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('comments', 'id')
                    ->where('course_code', $courseCode)
                    ->where('status', Comment::STATUS_APPROVED),
            ],
        ]);

        $target = isset($validated['parent_id'])
            ? Comment::query()->findOrFail($validated['parent_id'])
            : null;

        $needsReview = ContentModerator::needsReview($validated['content']);

        Comment::create([
            'user_id' => $request->user()->id,
            'course_code' => $validated['course_code'],
            'content' => $validated['content'],
            'status' => $needsReview ? Comment::STATUS_PENDING : Comment::STATUS_APPROVED,
            'parent_id' => $target?->parent_id ?? $target?->id,
            'reply_to_user_id' => $target?->user_id,
        ]);

        return back()->with('success', $needsReview
            ? 'ความคิดเห็นของคุณอยู่ระหว่างรอการตรวจสอบ'
            : 'เพิ่มความคิดเห็นเรียบร้อยแล้ว');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        abort_unless((int) $comment->user_id === (int) auth()->id(), 403);

        $comment->replies()->delete();
        $comment->delete();

        return back()->with('success', 'ลบความคิดเห็นเรียบร้อยแล้ว');
    }

    public function toggleLike(Request $request, Comment $comment): JsonResponse
    {
        abort_unless($comment->status === Comment::STATUS_APPROVED && $comment->course()->published()->exists(), 404);

        $result = $comment->likes()->toggle($request->user()->id);

        return response()->json([
            'liked' => count($result['attached']) > 0,
            'count' => $comment->likes()->count(),
        ]);
    }

    public function report(Request $request, Comment $comment): RedirectResponse
    {
        abort_if((int) $comment->user_id === (int) $request->user()->id, 403);
        abort_unless($comment->status === Comment::STATUS_APPROVED && $comment->course()->published()->exists(), 404);

        $validated = $request->validate([
            'reason' => ['bail', 'required', Rule::in(array_keys(Report::REASONS))],
            'details' => ['bail', 'nullable', 'string', 'max:500'],
        ]);

        Report::firstOrCreate(
            [
                'comment_id' => $comment->id,
                'user_id' => $request->user()->id,
            ],
            [
                'reason' => $validated['reason'],
                'details' => $validated['details'] ?? null,
                'status' => Report::STATUS_PENDING,
            ],
        );

        return back()->with('success', 'ขอบคุณสำหรับการรายงาน เจ้าหน้าที่กำลังดำเนินการตรวจสอบ');
    }
}
