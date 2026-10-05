@extends('layouts.auth')

@section('title', 'ตั้งชื่อผู้ใช้')
@section('content')
    <header>
        <h1 class="text-[22px] font-semibold leading-7 text-[#111111]">
            ตั้งชื่อผู้ใช้ของคุณ
        </h1>
        <p class="mt-2 text-sm leading-6 text-[#929292]">
            ระบบสุ่มชื่อผู้ใช้ให้คุณไว้ชั่วคราว — กดยืนยันเพื่อใช้ชื่อนี้ หรือพิมพ์ชื่อใหม่ที่เป็นของคุณเอง
        </p>
    </header>

    <form
        action="#"
        method="POST"
        class="mt-5"
        aria-label="แบบฟอร์มตั้งชื่อผู้ใช้"
    >
        @csrf
        <label
            for="username"
            class="mb-2 block text-sm text-slate-900"
        >
            ชื่อผู้ใช้
        </label>
        <div class="relative">
            <span
                class="pointer-events-none absolute inset-y-0 left-3 grid place-items-center text-slate-400"
                aria-hidden="true"
            >
                <i
                    data-lucide="at-sign"
                    class="size-4"
                ></i>
            </span>
            <input
                id="username"
                name="username"
                type="text"
                autocomplete="username"
                placeholder="กรอกชื่อผู้ใช้ของคุณ"
                class="h-[43px] w-full rounded-[11px] border border-[#bcbcbc] px-3 pl-9 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                value="{{ old('username', $suggestedUsername) }}"
            >
        </div>
        <p class="mt-2 text-xs text-slate-400">
            ใช้ได้แค่ a-z, A-Z, 0-9 และ _ (3–20 ตัวอักษร)
        </p>
        <button
            type="submit"
            class="mt-5 h-[44px] w-full rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-4 text-sm text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
        >
            ยืนยันชื่อผู้ใช้
        </button>
    </form>
@endsection
