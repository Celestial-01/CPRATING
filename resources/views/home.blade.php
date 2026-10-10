@extends('layouts.app')

@section('title', 'หน้าหลัก')

@section('content')
    <x-navbar :home-hero="true" />

    @if ($showSkeleton)
        <x-skeleton.home />
    @else
        <main class="bg-[#f8fafc]">
            <section
                class="home-hero relative flex min-h-[100svh] items-center overflow-hidden bg-[#f4f5f7]"
                aria-labelledby="home-title"
            >
                <div class="home-hero-glow pointer-events-none absolute inset-0" aria-hidden="true"></div>
                <span
                    class="absolute left-5 top-7 z-20 text-sm font-semibold tracking-[0.08em] text-slate-900 sm:left-8 sm:top-9"
                >
                    CP<span class="text-[#1684e5]">RATING</span>
                </span>

                <div
                    class="relative mx-auto flex min-h-[calc(100svh-5rem)] w-full max-w-[1240px] items-center justify-center
                        px-5 py-24 sm:px-8 lg:min-h-[100svh] lg:py-28"
                >
                    <div class="home-enter relative z-10 max-w-[690px] text-center">
                        <h1
                            id="home-title"
                            class="text-[clamp(2.35rem,6vw,5.25rem)] font-semibold leading-[1.12] tracking-[-0.025em] text-slate-950"
                        >
                            ทุกวิชามีเรื่องราว<br>
                            <span class="text-[#1684e5]">ก่อนคุณลงเรียน</span>
                        </h1>
                        <p
                            class="mx-auto mt-5 max-w-[480px] text-[15px] leading-7 text-slate-600 sm:mt-7 sm:text-lg sm:leading-9"
                        >
                            สำรวจรายวิชา อ่านประสบการณ์จากเพื่อน และเห็นภาพการเรียนให้ชัดขึ้น
                            ก่อนเลือกวิชาที่เหมาะกับคุณ
                        </p>
                    </div>

                    <div
                        class="home-hero-gallery pointer-events-none absolute inset-0"
                        aria-label="ภาพรายวิชาแนะนำ"
                    >
                        <div class="home-hero-row home-hero-row-top">
                            @foreach (range(1, 3) as $copy)
                                @foreach ($featuredCourses as $featuredCourse)
                                    <a
                                        href="{{ route('courses.show', $featuredCourse['code']) }}"
                                        class="home-hero-tile pointer-events-auto"
                                        aria-label="ดูรายละเอียดวิชา {{ $featuredCourse['title'] }}"
                                        @if ($copy > 1)
                                            tabindex="-1"
                                            aria-hidden="true"
                                        @endif
                                    >
                                        <img src="{{ $featuredCourse['image'] }}" alt="" loading="lazy">
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                        <div class="home-hero-row home-hero-row-bottom">
                            @foreach (range(1, 3) as $copy)
                                @foreach (array_reverse($featuredCourses) as $featuredCourse)
                                    <a
                                        href="{{ route('courses.show', $featuredCourse['code']) }}"
                                        class="home-hero-tile pointer-events-auto"
                                        aria-label="ดูรายละเอียดวิชา {{ $featuredCourse['title'] }}"
                                        @if ($copy > 1)
                                            tabindex="-1"
                                            aria-hidden="true"
                                        @endif
                                    >
                                        <img src="{{ $featuredCourse['image'] }}" alt="" loading="lazy">
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section
                id="featured-courses"
                class="home-section scroll-mt-24"
                aria-labelledby="featured-courses-title"
            >
                <div class="home-container">
                    <header class="home-section-heading" data-home-reveal>
                        <div>
                            <h2
                                id="featured-courses-title"
                                class="home-section-title"
                            >
                                เริ่มสำรวจรายวิชา
                            </h2>
                        </div>
                        <p class="home-section-description">
                            ทำความรู้จักเนื้อหาและบรรยากาศการเรียนของแต่ละวิชา
                        </p>
                    </header>

                    <div class="home-course-grid" data-home-reveal>
                        @foreach ($featuredCourses as $featuredCourse)
                            <a href="{{ route('courses.show', $featuredCourse['code']) }}" class="home-course-tile group">
                                <div class="home-course-image">
                                    <img src="{{ $featuredCourse['image'] }}" alt="" loading="lazy">
                                    <span class="home-course-code">{{ $featuredCourse['code'] }}</span>
                                </div>
                                <div class="mt-4">
                                    <p class="text-xs font-medium text-[#1684e5]">
                                        {{ $featuredCourse['category'] }}
                                    </p>
                                    <h3
                                        class="mt-1 text-base font-semibold leading-snug text-slate-900 transition-colors duration-300
                                            group-hover:text-[#1684e5] sm:text-lg"
                                    >
                                        {{ $featuredCourse['title'] }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $featuredCourse['credits'] }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            <section
                class="home-section border-y border-slate-200/80 bg-white"
                aria-labelledby="latest-courses-title"
            >
                <div class="home-container">
                    <header class="home-section-heading" data-home-reveal>
                        <div>
                            <h2
                                id="latest-courses-title"
                                class="home-section-title"
                            >
                                ดูรายละเอียดก่อนตัดสินใจ
                            </h2>
                        </div>
                        <p class="home-section-description">
                            เลือกอ่านข้อมูลรายวิชา คะแนน และมุมมองจากผู้เรียนในแต่ละหน้า
                        </p>
                    </header>

                    <div
                        class="grid gap-10 lg:grid-cols-[minmax(0,1.25fr)_minmax(0,0.75fr)] lg:gap-12"
                        data-home-reveal
                    >
                        @if ($courses[0] ?? null)
                            <a href="{{ route('courses.show', $courses[0]['code']) }}" class="group block">
                                <div class="home-story-image aspect-[1.45]">
                                    <img src="{{ $courses[0]['image'] }}" alt="" loading="lazy">
                                </div>
                                <p class="mt-5 text-xs font-semibold uppercase tracking-[0.14em] text-[#1684e5]">
                                    {{ $courses[0]['code'] }} · รายวิชาแนะนำ
                                </p>
                                <h3
                                    class="mt-2 max-w-2xl text-2xl font-semibold leading-snug tracking-tight text-slate-950
                                        transition-colors duration-300 group-hover:text-[#1684e5] sm:text-3xl"
                                >
                                    {{ $courses[0]['title'] }}
                                </h3>
                                <p class="mt-3 max-w-xl text-sm leading-7 text-slate-600 sm:text-base">
                                    ดูรายละเอียดรายวิชา คะแนนจากผู้เรียน และข้อมูลประกอบการตัดสินใจก่อนลงทะเบียน
                                </p>
                                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-slate-900">
                                    อ่านรายละเอียด
                                    <i
                                        data-lucide="arrow-up-right"
                                        class="size-4 transition-transform duration-500
                                            group-hover:translate-x-1 group-hover:-translate-y-1"
                                        aria-hidden="true"
                                    ></i>
                                </span>
                            </a>
                        @endif

                        <div class="divide-y divide-slate-200">
                            @foreach (array_slice($courses, 1) as $course)
                                <a
                                    href="{{ route('courses.show', $course['code']) }}"
                                    class="group grid grid-cols-[104px_minmax(0,1fr)] gap-4 py-5 first:pt-0
                                        sm:grid-cols-[132px_minmax(0,1fr)] sm:gap-5 lg:grid-cols-[112px_minmax(0,1fr)]
                                        xl:grid-cols-[132px_minmax(0,1fr)]"
                                >
                                    <div class="home-story-image aspect-square">
                                        <img src="{{ $course['image'] }}" alt="" loading="lazy">
                                    </div>
                                    <div class="min-w-0 self-center">
                                        <p class="text-xs font-medium text-[#1684e5]">
                                            {{ $course['code'] }}
                                        </p>
                                        <h3
                                            class="mt-1 text-base font-semibold leading-snug text-slate-900
                                                transition-colors duration-300 group-hover:text-[#1684e5]"
                                        >
                                            {{ $course['title'] }}
                                        </h3>
                                        <p class="mt-2 text-xs text-slate-500">
                                            {{ $course['rating'] }} คะแนน · {{ $course['learners'] }} วิว
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section
                class="home-section"
                aria-labelledby="popular-courses-title"
            >
                <div class="home-container">
                    <header class="home-section-heading" data-home-reveal>
                        <div>
                            <h2
                                id="popular-courses-title"
                                class="home-section-title"
                            >
                                วิชาแนะนำเพิ่มเติม
                            </h2>
                        </div>
                        <p class="home-section-description">
                            อีกสองวิชาที่น่าลองเปิดดูต่อจากรายการด้านบน
                        </p>
                    </header>

                    <div class="grid gap-5 md:grid-cols-2" data-home-reveal>
                        @foreach (array_slice($featuredCourses, 0, 2) as $featuredCourse)
                            <a
                                href="{{ route('courses.show', $featuredCourse['code']) }}"
                                class="home-popular-card group"
                            >
                                <div class="home-popular-image">
                                    <img src="{{ $featuredCourse['image'] }}" alt="" loading="lazy">
                                </div>
                                <div class="flex min-w-0 flex-1 flex-col justify-between gap-6 p-5 sm:p-7">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#1684e5]">
                                            {{ $featuredCourse['code'] }}
                                        </p>
                                        <h3
                                            class="mt-3 text-xl font-semibold leading-snug tracking-tight text-slate-950
                                                transition-colors duration-300 group-hover:text-[#1684e5] sm:text-2xl"
                                        >
                                            {{ $featuredCourse['title'] }}
                                        </h3>
                                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">
                                            {{ $featuredCourse['description'] }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900">
                                        ดูรายวิชา
                                        <i
                                            data-lucide="arrow-up-right"
                                            class="size-4 transition-transform duration-500
                                                group-hover:translate-x-1 group-hover:-translate-y-1"
                                            aria-hidden="true"
                                        ></i>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>
    @endif

    <x-footer :flush="true" />
@endsection
