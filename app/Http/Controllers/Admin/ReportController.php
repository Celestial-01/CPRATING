<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['status' => ['sometimes', Rule::in([...Report::STATUSES, 'all'])]]);
        $status = $request->query('status', 'pending');

        $reports = Report::with(['comment.user', 'reporter', 'handler'])
            ->when(
                in_array($status, ['pending', 'resolved', 'dismissed'], true),
                fn ($q) => $q->where('status', $status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact('reports', 'status'));
    }

    // ถ้าคอมเมนต์ที่รีพอร์ตมา ไม่ได้ผิด จะเปลี่ยน status เป็น dismissed คือคอมเมนต์นั้นยังอยู่ตามเดิม
    public function dismiss(Request $request, Report $report): RedirectResponse
    {
        $report->update([
            'status' => 'dismissed',
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ]);

        return back()->with('success', 'ปิดรายงานแล้ว (ไม่พบการละเมิด)');
    }

    // ถ้าผิดจริงจะถูกซ่อนเข้า removeComment
    public function removeComment(Request $request, Report $report): RedirectResponse
    {
        DB::transaction(function () use ($request, $report) {
            $report->comment->update(['status' => 'rejected']);

            Report::where('comment_id', $report->comment_id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'resolved',
                    'handled_by' => $request->user()->id,
                    'handled_at' => now(),
                ]);
        });

        return back()->with('success', 'ซ่อนความคิดเห็นและปิดรายงานที่เกี่ยวข้องแล้ว');
    }
}
