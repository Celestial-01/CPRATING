@extends('layouts.app')

@section('title', 'หน้าหลัก')

@section('content')
    <x-navbar />

    @if ($showSkeleton)
        <x-skeleton.home />
    @else
        <main class="mx-auto w-full max-w-[1120px] px-3 pb-0 sm:px-6">
            <section
                class="relative mt-6 overflow-hidden rounded-[24px] bg-white px-8 py-8 shadow-[0_10px_24px_rgba(15,23,42,0.12)] sm:mt-7 sm:px-12 sm:py-7 lg:px-14 lg:py-8"
                data-course-carousel
                tabindex="0"
                aria-roledescription="carousel"
                aria-label="คอร์สแนะนำ"
            >
                <div class="relative min-h-[360px] sm:min-h-[280px]">
                    @foreach ($featuredCourses as $index => $featuredCourse)
                        <article
                            @class([
                                'grid items-center gap-7 sm:min-h-[300px] sm:grid-cols-[190px_minmax(0,1fr)] lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-10',
                                'hidden' => $index !== 0,
                            ])
                            data-carousel-slide
                            aria-roledescription="slide"
                            aria-label="สไลด์ {{ $index + 1 }} จาก {{ count($featuredCourses) }}"
                            @if ($index !== 0)
                                aria-hidden="true"
                            @endif
                        >
                            <div class="relative mx-auto aspect-[0.82] w-full max-w-[220px] overflow-hidden rounded-[18px] sm:mx-0 sm:aspect-auto sm:max-w-none sm:self-stretch">
                                <img
                                    src="{{ $featuredCourse['image'] }}"
                                    alt="ภาพประกอบวิชา {{ $featuredCourse['title'] }}"
                                    class="absolute inset-0 size-full object-cover"
                                >
                            </div>

                            <div class="min-w-0 text-center sm:text-left">
                                <div class="flex flex-wrap justify-center gap-2 sm:justify-start">
                                    <span class="rounded-full bg-sky-50 px-3 py-1 text-[11px] font-medium text-[#1684e5]">
                                        {{ $featuredCourse['code'] }}
                                    </span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] text-slate-500">
                                        {{ $featuredCourse['category'] }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-[11px] text-slate-500">
                                        <i
                                            data-lucide="book-open"
                                            class="size-3.5"
                                            aria-hidden="true"
                                        ></i>
                                        {{ $featuredCourse['credits'] }}
                                    </span>
                                </div>

                                <h1 class="mt-3 text-2xl font-semibold leading-tight text-slate-900 sm:text-3xl">
                                    {{ $featuredCourse['title'] }}
                                </h1>
                                <p class="mt-1 text-xs uppercase tracking-[0.14em] text-slate-500">
                                    {{ $featuredCourse['englishTitle'] }}
                                </p>
                                <p class="mx-auto mt-4 max-w-xl text-sm leading-6 text-slate-500 sm:mx-0">
                                    {{ $featuredCourse['description'] }}
                                </p>

                                <div class="mt-3 flex flex-wrap justify-center gap-x-4 gap-y-2 text-xs text-slate-700 sm:justify-start">
                                    <span class="inline-flex items-center gap-1">
                                        <i
                                            data-lucide="star"
                                            class="size-4 fill-yellow-400 text-yellow-400"
                                            aria-hidden="true"
                                        ></i>
                                        {{ $featuredCourse['rating'] }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i
                                            data-lucide="eye"
                                            class="size-4"
                                            aria-hidden="true"
                                        ></i>
                                        {{ $featuredCourse['learners'] }} วิว
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i
                                            data-lucide="book-open"
                                            class="size-4"
                                            aria-hidden="true"
                                        ></i>
                                        {{ $featuredCourse['lessons'] }} วิชา
                                    </span>
                                </div>

                                <a
                                    href="{{ route('courses.show', $featuredCourse['code']) }}"
                                    class="mt-4 inline-flex rounded-[14px] bg-slate-950 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                                >
                                    อ่านเพิ่มเติม
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <button
                    type="button"
                    class="absolute left-2 top-1/2 z-10 grid size-10 -translate-y-1/2 place-items-center rounded-full border border-slate-300 bg-white text-slate-500 shadow-sm transition hover:border-sky-300 hover:text-[#1684e5] focus:outline-none focus:ring-4 focus:ring-sky-100 sm:left-4"
                    aria-label="คอร์สก่อนหน้า"
                    data-carousel-previous
                >
                    <i
                        data-lucide="chevron-left"
                        class="size-5"
                        aria-hidden="true"
                    ></i>
                </button>
                <button
                    type="button"
                    class="absolute right-2 top-1/2 z-10 grid size-10 -translate-y-1/2 place-items-center rounded-full border border-slate-300 bg-white text-slate-500 shadow-sm transition hover:border-sky-300 hover:text-[#1684e5] focus:outline-none focus:ring-4 focus:ring-sky-100 sm:right-4"
                    aria-label="คอร์สถัดไป"
                    data-carousel-next
                >
                    <i
                        data-lucide="chevron-right"
                        class="size-5"
                        aria-hidden="true"
                    ></i>
                </button>

                <div
                    class="mt-5 flex justify-center gap-1.5"
                    aria-label="เลือกคอร์สแนะนำ"
                >
                    @foreach ($featuredCourses as $index => $featuredCourse)
                        <button
                            type="button"
                            class="h-2.5 w-2.5 rounded-full bg-slate-300 transition-all data-[active=true]:w-10 data-[active=true]:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                            aria-label="เลือกสไลด์ที่ {{ $index + 1 }}"
                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                            data-active="{{ $index === 0 ? 'true' : 'false' }}"
                            data-carousel-dot
                        ></button>
                    @endforeach
                </div>
            </section>

            <section
                class="mt-8 sm:mt-10"
                aria-labelledby="latest-courses-title"
            >
                <x-section-heading
                    id="latest-courses-title"
                    icon="flame"
                    title="เนื้อหาอัปเดตล่าสุด"
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-5">
                    @foreach ($courses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                    @foreach (array_slice($courses, 0, 2) as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
            </section>

            <section
                class="mt-12 sm:mt-14"
                aria-labelledby="popular-courses-title"
            >
                <x-section-heading
                    id="popular-courses-title"
                    icon="star"
                    title="วิชายอดนิยม"
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-5">
                    @foreach ($courses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                    @foreach (array_slice($courses, 0, 2) as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
            </section>
        </main>
    @endif

    <x-footer />
@endsection

