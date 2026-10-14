@extends('layouts.admin')

@section('title', 'รายงานความคิดเห็น')

@section('content')
    @php
        $reasonLabels = [
            'spam'       => 'สแปม',
            'offensive'  => 'ไม่สุภาพ / ก้าวร้าว',
            'irrelevant' => 'ไม่เกี่ยวข้องกับรายวิชา',
            'other'      => 'อื่น ๆ',
        ];

        $statusTabs = [
            'pending'   => 'รอตรวจสอบ',
            'resolved'  => 'ซ่อนแล้ว',
            'dismissed' => 'ไม่พบการละเมิด',
            'all'       => 'ทั้งหมด',
        ];

        $statusBadges = [
            'pending'   => 'bg-amber-100 text-amber-700',
            'resolved'  => 'bg-red-100 text-red-700',
            'dismissed' => 'bg-gray-100 text-gray-600',
        ];
    @endphp

    <x-admin.page
        title="รายงานความคิดเห็น"
        description="ตรวจสอบความคิดเห็นที่ผู้ใช้รายงานเข้ามา"
    >
        {{-- แท็บกรองสถานะ --}}
        <div class="mb-6 flex flex-wrap gap-2">
            @foreach ($statusTabs as $key => $label)
                <a
                    href="{{ route('admin.reports.index', ['status' => $key]) }}"
                    class="rounded-full px-4 py-2 text-sm transition {{ $status === $key
                        ? 'bg-gradient-to-r from-[#1684df] to-[#56afff] text-white shadow-sm'
                        : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse ($reports as $report)
                @php $comment = $report->comment; @endphp

                <div class="rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusBadges[$report->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusTabs[$report->status] ?? $report->status }}
                            </span>
                            <span>รายงานเมื่อ {{ $report->created_at->format('d/m/Y H:i') }}</span>
                            <span>โดย {{ $report->reporter->name ?? 'ไม่ทราบ' }}</span>
                        </div>

                        <span class="text-sm text-gray-500">
                            วิชา {{ $comment->course_code }}
                        </span>
                    </div>

                    {{-- ความคิดเห็นที่ถูกรายงาน --}}
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="mb-1 text-xs text-gray-500">
                            ความคิดเห็นของ {{ $comment->user->name ?? 'ไม่ทราบ' }}
                            @if ($comment->status !== 'approved')
                                <span class="ml-1 text-red-500">(ไม่แสดงบนหน้าเว็บ)</span>
                            @endif
                        </p>
                        <p class="whitespace-pre-line break-words">{{ $comment->content }}</p>
                    </div>

                    <div class="mt-3 text-sm">
                        <span class="font-medium">เหตุผล:</span>
                        {{ $reasonLabels[$report->reason] ?? $report->reason }}

                        @if ($report->details)
                            <p class="mt-1 text-gray-600">{{ $report->details }}</p>
                        @endif
                    </div>

                    @if ($report->status === 'pending')
                        <div class="mt-4 flex flex-wrap gap-3">
                            <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="inline-flex h-[45px] items-center rounded-[15px] border border-gray-300 px-6 text-sm text-gray-700 transition hover:bg-gray-50"
                                >
                                    ไม่พบการละเมิด
                                </button>
                            </form>

                            <form
                                action="{{ route('admin.reports.remove-comment', $report) }}"
                                method="POST"
                                onsubmit="return confirm('ต้องการซ่อนความคิดเห็นนี้ใช่ไหม?')"
                            >
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="inline-flex h-[45px] items-center rounded-[15px] bg-red-500 px-6 text-sm text-white shadow-sm transition hover:brightness-105"
                                >
                                    ซ่อนความคิดเห็น
                                </button>
                            </form>
                        </div>
                    @elseif ($report->handled_at)
                        <p class="mt-4 text-xs text-gray-400">
                            จัดการโดย {{ $report->handler->name ?? 'ไม่ทราบ' }}
                            เมื่อ {{ $report->handled_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
            @empty
                <p class="w-full py-8 text-center text-gray-400">
                    ไม่มีรายงานในหมวดนี้
                </p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </x-admin.page>
@endsection