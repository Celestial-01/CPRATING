@extends('layouts.app')

@section('title', 'ค้นหารายวิชา')

@section('content')
    <x-navbar active="courses" />

    <main class="pg source-page" data-courses-page data-initial-query="{{ $query }}" data-page-size="{{ $perPage }}">
        <div class="wrap">
            <p class="eb">ค้นหาวิชา</p>
            <h1>ค้นหา<span class="k">รายวิชา</span></h1>
            <p class="ld">พิมพ์รหัสหรือชื่อวิชา หรือกรองตามสาขาที่สนใจ</p>

            <div class="tools">
                <label class="sbox">
                    <i data-lucide="search" class="ic size-5" aria-hidden="true"></i>
                    <input type="search" name="q" value="{{ $query }}" placeholder="ค้นหารหัสวิชา หรือชื่อวิชา" aria-label="ค้นหารายวิชา" data-course-search>
                </label>
                <div class="relative inline-flex h-12 w-full items-center rounded-full border border-[#dbe4ef] bg-white text-[#0b1220] transition-colors hover:border-[#93c5fd] focus-within:border-[#2b7fff] focus-within:ring-4 focus-within:ring-blue-100 sm:w-[210px]">
                    <i data-lucide="list-filter" class="pointer-events-none absolute left-4 size-4 text-[#657083]" aria-hidden="true"></i>
                    <select
                        data-course-filter
                        aria-label="กรองรายวิชาตามสาขา"
                        class="h-full w-full cursor-pointer appearance-none rounded-full bg-transparent pl-11 pr-10 text-sm font-medium outline-none"
                    >
                        <option value="all" @selected($selectedFilter === 'all')>ทั้งหมด</option>
                        <option value="popular" @selected($selectedFilter === 'popular')>วิชายอดนิยม</option>
                        @foreach ($majorCodes as $majorCode)
                            <option value="{{ $majorCode }}" @selected($selectedFilter === $majorCode)>{{ $majorCode }}</option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="pointer-events-none absolute right-4 size-4 text-[#657083]" aria-hidden="true"></i>
                </div>
                <p class="cnt" data-course-count aria-live="polite"></p>
            </div>

            <div class="cg" data-course-grid>
                @foreach ($courses as $course)
                    <x-course.card :course="$course" />
                @endforeach
            </div>

            <nav class="pgn" data-course-pagination aria-label="เลือกหน้า" hidden></nav>
            <div class="empty" data-course-empty hidden>
                <b>ไม่พบรายวิชาที่ตรงกับตัวกรอง</b>
                ลองค้นหาด้วยคำอื่นหรือเลือก "ทั้งหมด"
            </div>
        </div>
    </main>

    <div id="toast" role="status" aria-live="polite"></div>
    <script type="application/json" data-course-copy>@json($courseCopy)</script>
    <x-footer />
@endsection
