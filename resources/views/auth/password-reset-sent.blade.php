@extends('layouts.auth')

@section('title', 'ยืนยันอีเมล')
@section('content')
    <div class="auth-status-icon">
        <i
            data-lucide="key-round"
            class="size-7"
            aria-hidden="true"
        ></i>
    </div>

    <header class="text-center">
        <h1 class="text-xl font-semibold leading-8 text-slate-900">
            ตรวจสอบอีเมลของคุณ
        </h1>
        <p class="mt-2 text-sm text-slate-400">
            เราได้ส่งลิงก์รีเซ็ตรหัสผ่านไปยัง
        </p>
        <p class="mt-1 text-base font-medium text-slate-900">
            patiphan.nam@kkumail.com
        </p>
    </header>

    <aside
        class="auth-message mt-5 rounded-[15px] border px-4 py-3 text-sm leading-6"
    >
        กรุณาตรวจสอบอีเมลของคุณและคลิกลิงก์เพื่อดำเนินการรีเซ็ตรหัสผ่าน
        <br>
        <span class="text-xs">ลิงก์จะหมดอายุใน 15 นาที</span>
    </aside>

    <a
        href="{{ route('auth.login') }}"
        class="mt-5 flex h-[45px] items-center justify-center rounded-[15px] border border-[#bcbcbc] text-sm text-slate-500 transition hover:bg-slate-50"
    >
        กลับสู่หน้าเข้าสู่ระบบ
    </a>
@endsection
