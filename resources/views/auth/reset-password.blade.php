@extends('layouts.auth')

@section('title', 'รีเซ็ตรหัสผ่าน')
@section('content')
    <header>
        <h1 class="text-[22px] font-semibold leading-7 text-[#111111]">
            รีเซ็ตรหัสผ่าน
        </h1>
        <p class="mt-2 text-sm leading-6 text-[#929292]">
            กรอกรหัสผ่านใหม่ของคุณ
        </p>
    </header>

    <form
        action="{{ route('auth.reset-password.store') }}"
        method="POST"
        class="mt-6 space-y-5"
        aria-label="แบบฟอร์มรีเซ็ตรหัสผ่าน"
    >
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label
                for="reset-password"
                class="mb-2 block text-sm text-slate-900"
            >
                รหัสผ่านใหม่
            </label>
            <div class="relative">
                <input
                    id="reset-password"
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
                    aria-controls="reset-password"
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
                for="reset-password-confirmation"
                class="mb-2 block text-sm text-slate-900"
            >
                ยืนยันรหัสผ่านใหม่
            </label>
            <input
                id="reset-password-confirmation"
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
            รีเซ็ตรหัสผ่าน
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-400">
        จำรหัสผ่านได้แล้ว?
        <a
            href="{{ route('auth.login') }}"
            class="auth-action-link focus-visible:outline-none"
        >
            เข้าสู่ระบบ
        </a>
    </p>
@endsection
