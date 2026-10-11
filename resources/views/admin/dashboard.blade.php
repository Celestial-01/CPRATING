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

