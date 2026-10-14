@extends('layouts.auth')

@section('title', 'ลืมรหัสผ่าน')
@section('content')
    <header>
        <h1 class="text-[22px] font-semibold leading-7 text-[#111111]">
            ลืมรหัสผ่าน
        </h1>
        <p class="mt-2 text-sm leading-6 text-[#929292]">
            กรอกอีเมลของคุณเพื่อรับลิงก์รีเซ็ตรหัสผ่าน
        </p>
    </header>

    @if (session('reset_link_error'))
        <div
            class="mt-5 rounded-[12px] border border-red-200 bg-red-50 px-3 py-2.5 text-sm leading-5 text-red-700"
            role="alert"
        >
            {{ session('reset_link_error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-5 rounded-[12px] border border-red-200 bg-red-50 px-3 py-2.5 text-sm leading-6 text-red-700" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form
        action="{{ route('auth.forgot-password.store') }}"
        method="POST"
        class="mt-6"
        aria-label="แบบฟอร์มขอรีเซ็ตรหัสผ่าน"
    >
        @csrf
        <label
            for="forgot-email"
            class="mb-2 block text-sm text-slate-900"
        >
            อีเมล
        </label>
        <input
            id="forgot-email"
            name="email"
            type="email"
            data-auth-email-input
            autocomplete="email"
            placeholder="ชื่อผู้ใช้@kkumail.com"
            value="{{ old('email') }}"
            required
            class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
        >
        <p class="mt-2 text-xs text-slate-500">ใช้อีเมลที่ลงท้ายด้วย @kkumail.com</p>
        @error('email')
            <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
        <button
            type="submit"
            class="mt-5 h-[45px] w-full rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-4 text-sm text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
        >
            ส่งลิงก์รีเซ็ตรหัสผ่าน
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-400">
        จำรหัสผ่านได้แล้ว?
        <a
            href="{{ route('auth.login') }}"
            class="auth-action-link focus-visible:outline-none"
        >
            เข้าสู่ระบบ
        </a>
    </p>
@endsection
