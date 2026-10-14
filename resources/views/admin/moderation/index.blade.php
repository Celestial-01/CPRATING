@extends('layouts.admin')

@section('title', 'รออนุมัติ')

@section('content')
    @php
        $statusTabs = [
            'pending'  => 'รออนุมัติ',
            'approved' => 'ยืนยันแล้ว',
            'deleted'  => 'ลบแล้ว',
            'all'      => 'ทั้งหมด',
        ];

        $statusBadges = [
            'pending'  => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-green-100 text-green-700',
            'deleted'  => 'bg-red-100 text-red-700',
        ];
    @endphp

    <x-admin.page
        title="รออนุมัติ"
        description="ตรวจสอบความคิดเห็นที่มีคำสุ่มเสี่ยง ก่อนแสดงบนหน้าเว็บ"
    >
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- แท็บกรองสถานะ --}}
        <div class="mb-6 flex flex-wrap gap-2">
            @foreach ($statusTabs as $key => $label)
                <a
                    href="{{ route('admin.moderation.index', ['status' => $key]) }}"
                    class="rounded-full px-4 py-2 text-sm transition {{ $status === $key
                        ? 'bg-gradient-to-r from-[#1684df] to-[#56afff] text-white shadow-sm'
                        : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse ($comments as $comment)
                <div class="rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusBadges[$comment->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusTabs[$comment->status] ?? $comment->status }}
                            </span>
                            <span>{{ $comment->user->name ?? 'ไม่ทราบ' }}</span>
                            <span>{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <span class="text-sm text-gray-500">วิชา {{ $comment->course_code }}</span>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="whitespace-pre-line break-words">{{ $comment->content }}</p>
                    </div>

                    @if ($comment->status === 'pending')
                        <div class="mt-4 flex flex-wrap gap-3">
                            <form action="{{ route('admin.moderation.approve', $comment) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="inline-flex h-[45px] items-center rounded-[15px] bg-green-500 px-6 text-sm text-white shadow-sm transition hover:brightness-105"
                                >
                                    อนุมัติ
                                </button>
                            </form>

                            <form
                                action="{{ route('admin.moderation.destroy', $comment) }}"
                                method="POST"
                                onsubmit="return confirm('ต้องการลบความคิดเห็นนี้ใช่ไหม?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex h-[45px] items-center rounded-[15px] bg-red-500 px-6 text-sm text-white shadow-sm transition hover:brightness-105"
                                >
                                    ลบ
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <p class="w-full py-8 text-center text-gray-400">
                    ไม่มีความคิดเห็นในหมวดนี้
                </p>
            @endforelse
        </div>
    </x-admin.page>
@endsection