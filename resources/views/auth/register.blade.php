@extends('layouts.auth')

@section('title', 'สมัครสมาชิก')
@section('showBrand', 'true')
@section('content')
    <nav
        class="mb-9 grid grid-cols-2 rounded-[15px] bg-[#e9f5ff] p-1 text-[17px] font-normal"
        aria-label="การยืนยันตัวตน"
    >
        <a
            href="{{ route('auth.login') }}"
            class="rounded-[15px] px-4 py-2.5 text-center text-[#3d4a56] transition hover:text-[#1684e5]"
        >
            เข้าสู่ระบบ
        </a>
        <a
            href="{{ route('auth.register') }}"
            class="rounded-[15px] bg-white px-4 py-2.5 text-center text-[#1684e5] shadow-[0_0_0_5px_rgba(255,255,255,.55)]"
            aria-current="page"
        >
            สมัครสมาชิก
        </a>
    </nav>

    <header
        class="mb-7 text-center"
    >
        <h1
            class="text-[15px] font-normal text-[#929292]"
        >
            มาร่วมแบ่งปันแชร์ประสบการณ์การศึกษากันเถอะ!
        </h1>
    </header>

    <form
        action="#"
        method="POST"
        class="space-y-5"
        aria-label="แบบฟอร์มสมัครสมาชิก"
    >
        @csrf
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
                autocomplete="email"
                placeholder="กรอกอีเมล"
                class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
            >
        </div>
        <button
            type="submit"
            class="h-[45px] w-full rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-4 text-sm font-normal text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
        >
            ส่งลิงก์ยืนยันอีเมล
        </button>
    </form>

    <div
        class="my-6 flex items-center gap-3 text-xs text-[#a5a5a5]"
        role="separator"
        aria-label="หรือ"
    >
        <span class="h-px flex-1 bg-slate-200"></span>
        <span>หรือ</span>
        <span class="h-px flex-1 bg-slate-200"></span>
    </div>

    <a
        href="#"
        class="auth-google flex h-[45px] items-center justify-center gap-3 rounded-[15px] border border-[#bcbcbc] bg-white text-sm text-[#111] transition"
    >
        <img
            src="{{ asset('images/google-g.svg') }}"
            alt="Google"
            class="size-5 shrink-0"
        >
        ดำเนินการต่อด้วย Google
    </a>

    <aside
        class="auth-privacy mt-5 flex items-start gap-3 rounded-[15px] border px-3 py-2.5 text-[11px] leading-5"
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
                href="#"
                class="underline"
            >
                นโยบายความเป็นส่วนตัว
            </a>
            และ
            <a
                href="#"
                class="underline"
            >
                ข้อกำหนดการใช้งาน
            </a>
        </p>
    </aside>
@endsection
