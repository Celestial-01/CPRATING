@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
   

    <main class="mx-auto w-full max-w-[1120px] px-6 pb-10">

        @if (session('success'))
            <div class="mt-6 rounded-[15px] border border-[#bfddfa] bg-[#edf7ff] px-4 py-3 text-sm text-[#527ba8]">
                {{ session('success') }}
            </div>
        @endif

        <x-admin.filter-bar
            :action="route('admin.dashboard')"
            :current-major="$currentMajor"
            :types="$types"
            :current-types="$currentTypes"
            :keyword="$keyword"
            placeholder="ค้นหารายวิชา"
        />

        <h1 class="mt-8 text-2xl font-semibold leading-8 text-slate-900 sm:text-3xl">
            รายวิชาทั้งหมด
        </h1>

        <div class="mt-4 flex justify-end">
            <a
                href="{{ route('admin.courses.trashed') }}"
                class="inline-flex h-[42px] items-center gap-2 rounded-[12px] border border-slate-200 bg-white px-4 text-sm font-medium text-slate-600 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18" />
                    <path d="M8 6V4h8v2" />
                    <path d="m19 6-1 14H6L5 6" />
                    <path d="M10 11v5" />
                    <path d="M14 11v5" />
                </svg>
                รายการที่ถูกลบ
            </a>
        </div>

        <x-admin.major-tabs
            :majors="$majors"
            :current="$currentMajor"
            :keyword="$keyword"
            :types="$currentTypes"
        />

        <section class="mt-8" aria-labelledby="major-title">
            <h2 id="major-title" class="mb-5 text-lg text-slate-800">
                {{ $majors[$currentMajor] }} ( {{ $currentMajor }} )
            </h2>

            @if ($courses->isEmpty())
                <p class="rounded-[24px] bg-white px-6 py-10 text-center text-sm text-slate-400 shadow-sm">
                    ยังไม่มีรายวิชา
                </p>
            @else
                <div class="grid grid-cols-4 gap-5">
                    @foreach ($courses as $course)
                        <x-admin.course-card :course="$course" />
                    @endforeach
                </div>
            @endif
           
        </section>
        
    </main>
    
@endsection

