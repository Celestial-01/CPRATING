@extends('layouts.auth')

@section('title', 'ตั้งรหัสผ่าน')
@section('content')
    @if ($errors->any())
        <div class="mb-5 rounded-[12px] border border-red-200 bg-red-50 px-3 py-2.5 text-sm leading-6 text-red-700" role="alert">
            {{ $errors->first() }}
        </div>
    @endif
    <header>
        <h1 class="text-[22px] font-semibold leading-7 text-[#111111]">
            ตั้งรหัสผ่าน
        </h1>
        <p class="mt-2 text-sm leading-6 text-[#929292]">
            กรอกรหัสผ่านของคุณ
        </p>
    </header>

    <form
        action="{{ route('auth.set-password.store') }}"
        method="POST"
        class="mt-6 space-y-5"
        aria-label="แบบฟอร์มตั้งรหัสผ่าน"
    >
        @csrf
        <div>
            <label
                for="new-password"
                class="mb-2 block text-sm text-slate-900"
            >
                รหัสผ่าน
            </label>
            <div class="relative">
                <input
                    id="new-password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="กรอกรหัสผ่าน (อย่างน้อย 8 ตัวอักษร)"
                    class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] px-3 pr-11 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >
                <button
                    type="button"
                    class="absolute inset-y-0 right-3 text-[#a8a8a8] transition-colors hover:text-[#6f6f6f]"
                    aria-label="แสดงรหัสผ่าน"
                    aria-controls="new-password"
                    data-password-toggle
                >
                    <i
                        data-lucide="eye"
                        class="size-5"
                    ></i>
                </button>
            </div>
        </div>

        <div>
            <label
                for="confirm-password"
                class="mb-2 block text-sm text-slate-900"
            >
                ยืนยันรหัสผ่าน
            </label>
            <input
                id="confirm-password"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                placeholder="กรอกรหัสผ่านอีกครั้ง"
                class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
            >
        </div>

        <button
            type="submit"
            class="h-[45px] w-full rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-4 text-sm text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
        >
            ยืนยันข้อมูล
        </button>
    </form>
@endsection
