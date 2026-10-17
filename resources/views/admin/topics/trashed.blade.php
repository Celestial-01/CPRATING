
@extends('layouts.admin')

@section('title', 'หัวข้อที่ถูกลบ')

@section('content')
    <x-admin.page
        title="หัวข้อที่ถูกลบ"
        description="กู้คืนหัวข้อที่ลบไปแล้ว หรือลบออกจากระบบอย่างถาวร"
    >
        <div class="mb-5">
            <a
                href="{{ route('admin.topics.index') }}"
                class="inline-flex items-center rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-50"
            >
                ← กลับไปรายชื่อหัวข้อ
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @forelse ($topics as $topic)
                <div class="inline-flex min-h-[45px] items-center gap-3 rounded-full border border-gray-200 bg-gray-100 px-4 text-gray-700">
                    <span class="text-base">
                        {{ $topic->name }}
                    </span>

                    <form
                        action="{{ route('admin.topics.restore', $topic->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700 transition hover:bg-emerald-200"
                        >
                            กู้คืน
                        </button>
                    </form>

                    <form
                        action="{{ route('admin.topics.force-delete', $topic->id) }}"
                        method="POST"
                        onsubmit="return confirm('ยืนยันลบหัวข้อนี้อย่างถาวรหรือไม่? การกระทำนี้ไม่สามารถกู้คืนได้')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-700 transition hover:bg-red-200"
                        >
                            ลบถาวร
                        </button>
                    </form>
                </div>
            @empty
                <p class="w-full py-8 text-center text-gray-400">
                    ไม่มีหัวข้อที่ถูกลบ
                </p>
            @endforelse
        </div>
    </x-admin.page>
@endsection
