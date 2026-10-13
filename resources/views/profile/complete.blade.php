@extends('layouts.app')

@section('title', 'เพิ่มข้อมูลนักศึกษา')

@section('content')
    <main class="auth-scene flex min-h-screen flex-col items-center justify-center px-4 py-8 sm:px-6 sm:py-10" data-profile-page>
        <x-site-brand class="mb-6" />

        <section
            class="w-full max-w-[560px] rounded-[28px] border border-slate-200 bg-white px-5 py-7 shadow-[0_20px_55px_rgba(15,23,42,0.12)] sm:px-10 sm:py-9"
            aria-labelledby="profile-complete-title"
        >
            <div class="flex items-center justify-center gap-2 text-xs font-medium sm:text-sm" aria-label="ขั้นตอนการตั้งค่าบัญชี">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#edf7ff] px-3 py-1.5 text-slate-600">
                    <span class="grid size-5 place-items-center rounded-full bg-[#20bd68] text-white">
                        <i data-lucide="check" class="size-3.5" aria-hidden="true"></i>
                    </span>
                    เข้าสู่ระบบแล้ว
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#dceeff] px-3 py-1.5 text-[#0a70d8]">
                    <span class="grid size-5 place-items-center rounded-full bg-[#1684df] text-xs font-semibold text-white">2</span>
                    ข้อมูลนักศึกษา
                </span>
            </div>

            <header class="mt-7 text-center">
                <h1 id="profile-complete-title" class="text-[28px] font-semibold leading-tight text-slate-950 sm:text-[32px]">
                    เพิ่มข้อมูลนักศึกษา
                </h1>
                <p class="mx-auto mt-3 max-w-[420px] text-sm leading-6 text-slate-500 sm:text-[15px]">
                    ข้อมูลนี้ใช้สร้างแท็กของคุณในรีวิว และช่วยให้ชุมชนเห็นบริบทของผู้รีวิว
                </p>
            </header>

            <div class="mt-7 flex items-center gap-3 rounded-[18px] bg-[#f2f7fd] px-4 py-3.5 sm:px-5">
                <span class="relative grid size-12 shrink-0 place-items-center overflow-hidden rounded-full {{ $user->avatarColorClasses() }}">
                    <span class="text-lg font-semibold" aria-hidden="true">{{ $user->avatarInitial() }}</span>
                    @if ($user->avatarUrl())
                        <img
                            src="{{ $user->avatarUrl() }}"
                            alt="รูปโปรไฟล์ของ {{ $user->displayName() }}"
                            class="absolute inset-0 size-full object-cover"
                            referrerpolicy="no-referrer"
                            data-avatar-image
                        >
                    @endif
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-900">{{ $user->displayName() }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                </div>
                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-white px-3 py-1.5 text-xs font-medium text-[#0a70d8] shadow-[0_1px_4px_rgba(15,23,42,0.08)]">
                    @if ($user->googleAccount)
                        <i data-lucide="lock-keyhole" class="size-3.5" aria-hidden="true"></i>
                        จาก Google
                    @else
                        <i data-lucide="mail" class="size-3.5" aria-hidden="true"></i>
                        อีเมลที่ยืนยันแล้ว
                    @endif
                </span>
            </div>

            <form
                action="{{ route('profile.complete.store') }}"
                method="POST"
                class="mt-7 space-y-5"
                aria-label="แบบฟอร์มข้อมูลนักศึกษา"
                data-profile-form
            >
                @csrf

                @if ($errors->any())
                    <div class="flex items-start gap-2.5 rounded-[12px] border border-red-200 bg-red-50 px-3 py-2.5 text-sm leading-5 text-red-700" role="alert">
                        <i data-lucide="circle-alert" class="mt-0.5 size-4 shrink-0" aria-hidden="true"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div>
                    <label for="student_id" class="mb-2 block text-sm font-medium text-slate-900">
                        รหัสนักศึกษา
                    </label>
                    <div class="relative">
                        <input
                            id="student_id"
                            name="student_id"
                            type="text"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="69xxxxxxx-x"
                            value="{{ old('student_id', $user->student_id) }}"
                            pattern="[0-9]{9}-[0-9]"
                            maxlength="11"
                            required
                            data-student-id-input
                            class="h-13 w-full rounded-[15px] border border-transparent bg-[#f2f6fb] px-4 pr-12 text-sm tracking-[0.08em] text-slate-900 outline-none transition placeholder:tracking-normal placeholder:text-slate-400 focus:border-[#1684df] focus:bg-white focus:ring-4 focus:ring-blue-100 @error('student_id') border-red-400 bg-red-50 @enderror"
                        >
                        <span
                            class="pointer-events-none absolute right-4 top-1/2 hidden size-6 -translate-y-1/2 place-items-center rounded-full bg-[#20bd68] text-white"
                            data-student-id-valid
                            aria-hidden="true"
                        >
                            <i data-lucide="check" class="size-4" aria-hidden="true"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        ตัวอย่าง <span class="font-medium text-slate-700">69xxxxxxx-x</span> ระบบจะสร้างแท็กชั้นปีให้อัตโนมัติ
                    </p>
                </div>

                <div>
                    <label for="major_code" class="mb-2 block text-sm font-medium text-slate-900">
                        สาขา
                    </label>
                    <div class="relative">
                        <select
                            id="major_code"
                            name="major_code"
                            required
                            data-major-select
                            class="h-13 w-full cursor-pointer appearance-none rounded-[15px] border border-transparent bg-[#f2f6fb] px-4 pr-11 text-sm text-slate-900 outline-none transition focus:border-[#1684df] focus:bg-white focus:ring-4 focus:ring-blue-100 @error('major_code') border-red-400 bg-red-50 @enderror"
                        >
                            <option value="">เลือกสาขาของคุณ</option>
                            @foreach ($majorOptions as $majorCode => $majorName)
                                <option value="{{ $majorCode }}" @selected(old('major_code', $user->major_code) === $majorCode)>
                                    {{ $majorCode }} — {{ $majorName }}
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="pointer-events-none absolute right-4 top-1/2 size-4 -translate-y-1/2 text-slate-500" aria-hidden="true"></i>
                    </div>
                </div>

                <aside class="rounded-[18px] border border-dashed border-[#afd2f5] px-4 py-3.5" aria-live="polite">
                    <p class="text-xs text-slate-500">ตัวอย่างแท็กที่จะแสดงในรีวิว</p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="relative grid size-8 shrink-0 place-items-center overflow-hidden rounded-full {{ $user->avatarColorClasses() }}">
                            <span class="text-sm font-semibold" aria-hidden="true">{{ $user->avatarInitial() }}</span>
                            @if ($user->avatarUrl())
                                <img src="{{ $user->avatarUrl() }}" alt="" class="absolute inset-0 size-full object-cover" referrerpolicy="no-referrer" data-avatar-image>
                            @endif
                        </span>
                        <strong class="max-w-[130px] truncate text-sm text-slate-900">{{ $user->firstName() }}</strong>
                        <span class="rounded-full bg-[#f1f5f9] px-2.5 py-1 text-[11px] font-medium text-slate-500" data-profile-cohort>—</span>
                        <span class="rounded-full bg-[#e1f0ff] px-2.5 py-1 text-[11px] font-medium text-[#1684df]" data-profile-major>—</span>
                    </div>
                </aside>

                <button
                    type="submit"
                    class="inline-flex h-13 w-full cursor-pointer items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#0878ed] to-[#2e94f4] px-4 text-sm font-semibold text-white shadow-[0_14px_26px_rgba(22,132,223,0.25)] transition hover:-translate-y-px hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
                >
                    บันทึกข้อมูลและเริ่มใช้งาน
                    <i data-lucide="arrow-right" class="size-4" aria-hidden="true"></i>
                </button>
            </form>
        </section>
    </main>
@endsection
