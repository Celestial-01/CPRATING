@extends('layouts.app')

@section('title', 'ค้นหารายวิชา')

@section('content')
    <x-navbar active="courses" />

    <main class="content-page source-page" data-courses-page>
        <div class="page-container">
            <p class="section-eyebrow">ค้นหาวิชา</p>
            <h1>ค้นหา<span class="gradient-text">รายวิชา</span></h1>
            <p class="page-lead">พิมพ์รหัสหรือชื่อวิชา หรือกรองตามสาขาที่สนใจ</p>

            @if (session('rating_status'))
                <p class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                    {{ session('rating_status') }}
                </p>
            @endif

            <form class="course-filter-tools" action="{{ route('courses.index') }}" method="GET" data-course-filters>
                <label class="course-search-box">
                    <i data-lucide="search" class="ui-icon size-5" aria-hidden="true"></i>
                    <input type="search" name="q" value="{{ $query }}" maxlength="100" placeholder="ค้นหารหัสวิชา หรือชื่อวิชา" aria-label="ค้นหารายวิชา" data-course-search>
                </label>
                <div class="relative inline-flex h-12 w-full items-center rounded-full border border-[#dbe4ef] bg-white text-[#0b1220] transition-colors hover:border-[#93c5fd] focus-within:border-[#2b7fff] focus-within:ring-4 focus-within:ring-blue-100 sm:w-[210px]">
                    <i data-lucide="list-filter" class="pointer-events-none absolute left-4 size-4 text-[#657083]" aria-hidden="true"></i>
                    <select
                        data-course-filter
                        name="category"
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
                <p class="course-results-count" aria-live="polite">
                    {{ $courses->total() ? 'แสดง '.number_format($courses->firstItem()).'–'.number_format($courses->lastItem()).' จาก '.number_format($courses->total()).' วิชา' : 'ไม่พบวิชา' }}
                </p>
                <button type="submit" class="sr-only">ค้นหา</button>
            </form>

            <div class="course-grid" data-course-grid>
                @foreach ($courses as $course)
                    <x-course.card :course="$course" />
                @endforeach
            </div>

            <div class="mt-8">{{ $courses->onEachSide(1)->links() }}</div>
            @if ($courses->isEmpty())
            <div class="course-empty-state" data-course-empty>
                <b>ไม่พบรายวิชาที่ตรงกับตัวกรอง</b>
                ลองค้นหาด้วยคำอื่นหรือเลือก "ทั้งหมด"
            </div>
            @endif
        </div>
    </main>

    <div id="toast" role="status" aria-live="polite"></div>
    <script type="application/json" data-course-copy>@json($courseCopy)</script>
    <x-footer />
@endsection
