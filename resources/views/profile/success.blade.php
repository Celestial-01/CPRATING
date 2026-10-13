@extends('layouts.app')

@section('title', 'ยินดีต้อนรับ')

@section('content')
    <main class="auth-scene flex min-h-screen flex-col items-center justify-center px-4 py-8 sm:px-6 sm:py-10">
        <x-site-brand class="mb-6" />

        <section
            class="w-full max-w-[520px] rounded-[28px] border border-slate-200 bg-white px-6 py-9 text-center shadow-[0_20px_55px_rgba(15,23,42,0.12)] sm:px-10 sm:py-10"
            aria-labelledby="profile-success-title"
        >
            <div class="mx-auto grid size-[76px] place-items-center rounded-full bg-gradient-to-br from-[#2697f5] to-[#0872e8] text-white shadow-[0_16px_30px_rgba(22,132,223,0.28)]">
                <i data-lucide="check" class="size-10" stroke-width="2.5" aria-hidden="true"></i>
            </div>

            <h1 id="profile-success-title" class="mt-6 text-[28px] font-semibold leading-tight text-slate-950 sm:text-[32px]">
                ยินดีต้อนรับ {{ $user->firstName() }}
            </h1>
            <p class="mt-3 text-sm leading-6 text-slate-500">
                นี่คือแท็กที่จะแสดงข้างชื่อของคุณในรีวิวและความคิดเห็น
            </p>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-2 rounded-[18px] border border-slate-200 bg-[#f8fbff] px-4 py-4">
                <span class="grid size-10 shrink-0 place-items-center overflow-hidden rounded-full bg-[#dceeff] text-[#0a7ae6]">
                    @if ($user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="" class="size-full object-cover" referrerpolicy="no-referrer">
                    @else
                        <i data-lucide="user-round" class="size-5" aria-hidden="true"></i>
                    @endif
                </span>
                <strong class="text-sm text-slate-900">{{ $user->firstName() }}</strong>
                @foreach ($user->profileTags() as $tag)
                    <span class="rounded-full {{ str_starts_with($tag, 'kku') ? 'bg-slate-100 text-slate-600' : 'bg-[#e1f0ff] text-[#1684df]' }} px-2.5 py-1 text-[11px] font-medium">
                        {{ str_starts_with($tag, 'kku') ? 'KKU '.substr($tag, 3) : $tag }}
                    </span>
                @endforeach
            </div>

            <a
                href="{{ route('courses.index') }}"
                class="mt-6 inline-flex h-13 w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#0878ed] to-[#2e94f4] px-4 text-sm font-semibold text-white shadow-[0_14px_26px_rgba(22,132,223,0.25)] transition hover:-translate-y-px hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
            >
                เริ่มค้นหารายวิชา
                <i data-lucide="arrow-right" class="size-4" aria-hidden="true"></i>
            </a>
            <a
                href="{{ route('home') }}"
                class="mt-5 inline-flex text-sm font-medium text-[#0a70d8] underline-offset-4 transition hover:underline focus:outline-none focus-visible:underline"
            >
                กลับหน้าหลัก
            </a>
        </section>
    </main>
@endsection
