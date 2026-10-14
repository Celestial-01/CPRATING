@extends('layouts.auth')

@section('title', 'เข้าสู่ระบบ')
@section('showBrand', 'true')
@section('content')
    <nav
        class="auth-tabs mb-9 grid grid-cols-2 rounded-[15px] bg-[#e9f5ff] p-1 text-[17px] font-normal"
        data-active="login"
        aria-label="การยืนยันตัวตน"
    >
        <a
            href="{{ route('auth.login') }}"
            class="auth-tabs__link rounded-[15px] px-4 py-2.5 text-center"
            aria-current="page"
            data-auth-tab="login"
        >
            เข้าสู่ระบบ
        </a>
        <a
            href="{{ route('auth.register') }}"
            class="auth-tabs__link rounded-[15px] px-4 py-2.5 text-center"
            data-auth-tab="register"
        >
            สมัครสมาชิก
        </a>
    </nav>

    @if (session('status'))
        <div
            class="mb-5 flex items-start gap-2.5 rounded-[12px] border border-sky-200 bg-sky-50 px-3 py-2.5 text-sm leading-5 text-sky-700"
            role="status"
            aria-live="polite"
        >
            <i
                data-lucide="info"
                class="mt-0.5 size-4 shrink-0"
                aria-hidden="true"
            ></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <header
        class="mb-7 text-center"
    >
        <h1
            class="text-[15px] font-normal text-[#929292]"
        >
            ยินดีต้อนรับกลับ! โปรดกรอกข้อมูลเพื่อเข้าสู่ระบบ
        </h1>
    </header>

    <form
        action="{{ route('auth.login.store') }}"
        method="POST"
        class="space-y-5"
        aria-label="แบบฟอร์มเข้าสู่ระบบ"
    >
        @csrf
        @if ($errors->any())
            <div class="rounded-[12px] border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600" role="alert">
                {{ $errors->first() }}
            </div>
        @endif
        <div>
            <label
                for="email"
                class="mb-2 block text-[15px] font-normal text-[#111]"
            >
                อีเมล
            </label>
            <input
                id="email"
                name="email"
                type="email"
                data-auth-email-input
                autocomplete="email"
                placeholder="ชื่อผู้ใช้@kkumail.com"
                class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100 @error('email') border-red-400 @enderror"
            >
            <p class="mt-2 text-xs text-slate-500">ใช้บัญชีที่ลงท้ายด้วย @kkumail.com</p>
        </div>
        <div>
            <label
                for="password"
                class="mb-2 block text-[15px] font-normal text-[#111]"
            >
                รหัสผ่าน
            </label>
            <div
                class="relative"
            >
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="กรอกรหัสผ่าน"
                    class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >
                <button
                    type="button"
                    class="absolute inset-y-0 right-3 text-[#a8a8a8] transition-colors hover:text-[#6f6f6f] focus:outline-none focus-visible:text-[#4f4f4f]"
                    aria-label="แสดงรหัสผ่าน"
                    aria-controls="password"
                    data-password-toggle
                >
                    <i
                        data-lucide="eye"
                        class="size-5"
                    ></i>
                </button>
            </div>
        </div>
        <div
            class="flex items-center justify-between"
        >
            <label
                class="flex items-center gap-2 text-sm text-slate-600"
            >
                <input
                    type="checkbox"
                    name="remember"
                    class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                >
                จดจำ
            </label>
            <a
                href="{{ route('auth.forgot-password') }}"
                class="auth-action-link text-sm font-medium"
            >
                ลืมรหัสผ่าน?
            </a>
        </div>
        <button
            type="submit"
            class="h-[45px] w-full rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-4 text-sm font-normal text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
        >
            เข้าสู่ระบบ
        </button>
    </form>

    <div
        class="my-6 flex items-center gap-3 text-xs text-[#a5a5a5]"
        role="separator"
        aria-label="หรือ"
    >
        <span
            class="h-px flex-1 bg-slate-200"
        ></span>
        <span>หรือ</span>
        <span
            class="h-px flex-1 bg-slate-200"
        ></span>
    </div>

    <a
        href="{{ route('auth.google.redirect') }}"
                class="flex h-[45px] items-center justify-center gap-3 rounded-[15px] border border-[#bcbcbc] bg-white text-sm text-[#111] transition-colors duration-150 hover:border-[#9f9f9f] hover:bg-[#eeeeee] hover:shadow-[0_1px_2px_rgba(15,23,42,0.08)] focus-visible:outline-2 focus-visible:outline-[#9f9f9f] focus-visible:outline-offset-2"
    >
        <img
            src="{{ asset('images/google-g.svg') }}"
            alt="Google"
            class="size-5 shrink-0"
        >
        ดำเนินการต่อด้วย Google
    </a>
    <p class="mt-2 text-center text-xs text-slate-500">เลือกบัญชี Google ที่ใช้ @kkumail.com</p>

    <aside
                class="mt-5 flex items-start gap-3 rounded-[15px] border border-[#bfddfa] bg-[#edf7ff] px-3 py-2.5 text-[11px] leading-5 text-[#527ba8]"
    >
        <span
            class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-[#c5e8ff] text-[#1684df]"
        >
            <i
                data-lucide="sparkle"
                class="size-3 fill-current"
            ></i>
        </span>
        <p>
            ข้อมูลของคุณปลอดภัยและได้รับการปกป้องตาม
            <a
                href="#privacy"
                data-legal-modal-trigger="privacy"
                class="auth-legal-link"
            >
                นโยบายความเป็นส่วนตัว
            </a>
            และ
            <a
                href="#terms"
                data-legal-modal-trigger="terms"
                class="auth-legal-link"
            >
                ข้อกำหนดการใช้งาน
            </a>
        </p>
    </aside>

    @if (session('google_error'))
        <dialog
            class="m-auto w-[calc(100%-2rem)] max-w-[430px] translate-y-2 scale-[0.98]
                rounded-[24px] border border-red-100 bg-white p-0 text-slate-800 opacity-0
                shadow-[0_24px_70px_rgba(15,23,42,0.22)] transition-[opacity,transform]
                duration-200 ease-out backdrop:bg-slate-950/45 data-[open=true]:translate-y-0
                data-[open=true]:scale-100 data-[open=true]:opacity-100 motion-reduce:transition-none"
            data-google-error-dialog
            aria-labelledby="google-error-title"
            aria-describedby="google-error-message"
        >
            <div class="px-6 pb-6 pt-5 sm:px-8 sm:pb-8 sm:pt-7">
                <div class="flex items-start justify-between gap-4">
                    <span class="grid size-12 place-items-center rounded-full bg-red-50 text-red-600" aria-hidden="true">
                        <i data-lucide="circle-alert" class="size-6"></i>
                    </span>
                    <button
                        type="button"
                        class="grid size-9 cursor-pointer place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-300"
                        data-google-error-close
                        aria-label="ปิดหน้าต่างแจ้งข้อผิดพลาด"
                    >
                        <i data-lucide="x" class="size-5" aria-hidden="true"></i>
                    </button>
                </div>

                <h2 id="google-error-title" class="mt-5 text-xl font-semibold text-slate-900">
                    ดำเนินการด้วย Google ไม่สำเร็จ
                </h2>
                <p id="google-error-message" class="mt-2 text-sm leading-6 text-slate-600">
                    {{ session('google_error') }}
                </p>

                <div class="mt-6 grid gap-2.5">
                    <a
                        href="{{ route('auth.google.redirect') }}"
                        class="flex min-h-11 items-center justify-center rounded-[13px] bg-[#1684df] px-4 text-sm font-semibold text-white transition hover:bg-[#0a70d8] focus:outline-none focus-visible:ring-4 focus-visible:ring-sky-200"
                    >
                        ลองดำเนินการด้วย Google อีกครั้ง
                    </a>
                    <button
                        type="button"
                        class="min-h-11 cursor-pointer rounded-[13px] border border-slate-200 px-4 text-sm font-medium text-slate-600 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-sky-100"
                        data-google-error-close
                    >
                        กลับไปเข้าสู่ระบบด้วยอีเมล
                    </button>
                </div>
            </div>
        </dialog>

        <noscript>
            <p class="mt-5 rounded-[13px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                {{ session('google_error') }}
            </p>
        </noscript>
    @endif
@endsection
