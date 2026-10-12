@extends('layouts.auth')

@section('title', 'ยืนยันอีเมล')
@section('content')
    <div class="mx-auto mb-[18px] grid size-[58px] place-items-center rounded-full bg-gradient-to-br from-[#b7e5ff] to-[#e8f7ff] text-[#1684df]">
        <i
            data-lucide="mail"
            class="size-7"
            aria-hidden="true"
        ></i>
    </div>

    <header class="text-center">
        <h1 class="text-xl font-semibold leading-8 text-slate-900">
            ตรวจสอบอีเมลของคุณ
        </h1>
        <p class="mt-2 text-sm text-slate-400">
            เราได้ส่งลิงก์ยืนยันไปยัง
        </p>
        <p class="mt-1 break-all text-base font-medium text-slate-900">
            {{ $email ?: 'อีเมลของคุณ' }}
        </p>
    </header>

    <aside
        class="mt-5 rounded-[15px] border border-[#bcbcbc] px-4 py-3 text-sm leading-6 text-[#929292]"
    >
        กรุณาตรวจสอบอีเมลของคุณและคลิกลิงก์เพื่อดำเนินการสมัครสมาชิกต่อ
        <br>
        <span class="text-xs">ลิงก์จะหมดอายุใน 15 นาที</span>
    </aside>

    @if (session('verification_resent'))
        <div
            class="mt-4 flex items-center gap-2 rounded-[12px] border border-[#bfddfa] bg-[#edf7ff] px-3 py-2.5 text-sm leading-5 text-[#1684df]"
            role="status"
            aria-live="polite"
        >
            <i
                data-lucide="check"
                class="size-4 shrink-0"
                aria-hidden="true"
            ></i>
            <span>ส่งลิงก์ยืนยันไปยังอีเมลของคุณอีกครั้งแล้ว</span>
        </div>
    @endif

    @if (session('verification_resend_error'))
        <div
            class="mt-4 flex items-center gap-2 rounded-[12px] border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm leading-5 text-amber-700"
            role="alert"
            aria-live="polite"
        >
            <i
                data-lucide="clock-3"
                class="size-4 shrink-0"
                aria-hidden="true"
            ></i>
            <span>{{ session('verification_resend_error') }}</span>
        </div>
    @endif

    <a
        href="{{ route('auth.register') }}"
        class="auth-secondary-link mt-5 flex h-[45px] items-center justify-center rounded-[15px] border border-[#bcbcbc] text-sm transition hover:bg-slate-50"
    >
        กลับสู่หน้าสมัครสมาชิก
    </a>

    <form
        action="{{ route('auth.verify-email.resend') }}"
        method="POST"
        class="mt-5 text-center"
    >
        @csrf
        <p class="text-sm leading-6 text-slate-400">
            ยังไม่พบอีเมลใช่ไหม?
            <button
                type="submit"
                class="auth-resend-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-200 focus-visible:ring-offset-2"
            >
                ส่งลิงก์ยืนยันอีกครั้ง
            </button>
        </p>
    </form>
@endsection
