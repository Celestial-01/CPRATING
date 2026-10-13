@extends('layouts.auth')

@section('title', 'ดำเนินการไม่สำเร็จ')
@section('content')
    <div class="mx-auto grid size-[58px] place-items-center rounded-full bg-red-50 text-red-600">
        <i data-lucide="circle-alert" class="size-7" aria-hidden="true"></i>
    </div>

    <header class="mt-5 text-center">
        <h1 class="text-xl font-semibold text-slate-900">{{ $errorTitle }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $errorMessage }}</p>
    </header>

    <a
        href="{{ route($recoveryRoute) }}"
        class="mt-6 flex h-[45px] items-center justify-center rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-4 text-sm font-medium text-white transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
    >
        {{ $recoveryLabel }}
    </a>

    <a
        href="{{ route('home') }}"
        class="auth-secondary-link mt-3 flex h-[45px] items-center justify-center rounded-[15px] border border-[#bcbcbc] text-sm transition hover:bg-slate-50"
    >
        กลับหน้าหลัก
    </a>
@endsection
