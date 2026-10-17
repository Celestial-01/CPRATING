@extends('layouts.admin')

@section('title', 'รายชื่อหัวข้อ')

@section('content')
    <x-admin.page
        title="รายชื่อหัวข้อ"
        description="จัดการข้อมูลประเด็นหัวข้อต่าง ๆ ในรายวิชาเรียน"
    >
       
    <div class="mb-5">
        <a
            href="{{ route('admin.topics.trashed') }}"
            class="inline-flex h-[45px] items-center gap-2 rounded-[15px] border border-slate-200 bg-white px-5 text-sm font-medium text-slate-600 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-4 focus:ring-red-100"
        >
            รายการที่ถูกลบ
        </a>
    </div>

        <form action="{{ route('admin.topics.store') }}" method="POST"
              class="mb-6 flex items-end gap-3">
            @csrf

            <div class="flex-1">
                <label for="new-topic-name"
                       class="mb-2 block text-sm font-medium">
                    ชื่อหัวข้อใหม่
                </label>

                <input
                    id="new-topic-name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="เช่น Network"
                    required
                    class="h-[45px] w-full rounded-xl border border-gray-300 px-3 outline-none focus:border-sky-500"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="inline-flex h-[45px] items-center rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-6 text-sm text-white shadow-sm transition hover:brightness-105"
            >
                + เพิ่มหัวข้อ
            </button>
        </form>

        <div class="flex flex-wrap items-center gap-3">
            @forelse ($topics as $topic)
                <div
                    class="inline-flex min-h-[45px] items-center gap-2 rounded-full bg-gradient-to-r from-[#1A80D8] to-[#5AB2FF] px-4 text-white shadow-sm"
                >
                    {{-- data-editing: JS สลับ true/false, ส่วน label/input สลับการแสดงผลด้วย group-data --}}
                    <form
                        action="{{ route('admin.topics.update', $topic) }}"
                        method="POST"
                        data-topic-edit
                        data-editing="false"
                        class="group/topic flex min-w-0 items-center"
                    >
                        @csrf
                        @method('PUT')
                        <span
                            data-topic-label
                            title="ดับเบิลคลิกเพื่อแก้ไข"
                            class="cursor-text select-none text-[20px] font-normal group-data-[editing=true]/topic:hidden"
                        >{{ $topic->name }}</span>

                        <input
                            type="text"
                            name="name"
                            value="{{ $topic->name }}"
                            data-topic-input
                            aria-label="แก้ไขชื่อหัวข้อ"
                            class="hidden w-full min-w-0 rounded-md bg-white/95 px-2 py-1 text-base text-sky-800 outline-none group-data-[editing=true]/topic:block"
                            required
                            maxlength="255"
                        >
                    </form>

                    <form
                        action="{{ route('admin.topics.destroy', $topic) }}"
                        method="POST"
                        onsubmit="return confirm('ต้องการลบหัวข้อนี้และนำออกจากรายวิชาที่เกี่ยวข้องใช่ไหม?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            aria-label="ลบ {{ $topic->name }}"
                            title="ลบหัวข้อ"
                            onmousedown="event.preventDefault()"
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[28px] font-medium leading-none text-white transition hover:bg-white/20"
                        >
                            ×
                        </button>
                    </form>
                </div>
            @empty
                <p class="w-full py-8 text-center text-gray-400">
                    ยังไม่มีหัวข้อ
                </p>
            @endforelse
        </div>

        <p class="mt-4 text-xs text-gray-400">
            ดับเบิลคลิกชื่อหัวข้อเพื่อแก้ไข กด Enter หรือคลิกออกเพื่อบันทึก
        </p>
    </x-admin.page>
  
@endsection
