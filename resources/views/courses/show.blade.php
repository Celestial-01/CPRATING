@extends('layouts.app')

@section('title', $course['code'].' '.$course['title'])

@section('content')
    <div class="min-h-screen bg-[#f6f6f6]">
        <x-navbar active="courses" />

    <main class="relative z-0 mx-auto mt-2 w-full max-w-[1120px] px-3 pb-20 pt-0 sm:-mt-2 sm:px-6 sm:pt-4">
        <nav
            class="mt-0 flex items-center gap-2 text-xs text-slate-500 sm:mt-1 sm:text-sm"
            aria-label="เส้นทางการนำทาง"
        >
            <a
                href="{{ route('home') }}"
                class="transition hover:text-[#1684e5]"
            >
                หน้าหลัก
            </a>
            <i
                data-lucide="chevron-right"
                class="size-3.5"
                aria-hidden="true"
            ></i>
            <span class="font-medium text-slate-900">
                รายละเอียดวิชา {{ $course['code'] }}
            </span>
        </nav>
    
        @if ($showSkeleton)
            <x-skeleton.course-detail class="mt-6" />
        @else
            <x-course.summary
                :course="$course"
                :has-rated="$hasRated"
                class="mt-6"
            />

            @auth
                @unless ($hasRated)
                    <x-course.rating-modal :course="$course" />
                @endunless
            @endauth

        <section
            class="mt-6 grid gap-5 md:grid-cols-2"
            aria-label="ข้อมูลการเรียน"
        >
            @foreach ($courseOverviewStats as $courseOverviewStat)
                <x-course.overview-stat :stat="$courseOverviewStat" />
            @endforeach
        </section>

        <section
            class="mt-6 rounded-[22px] border border-slate-200 bg-white p-6 shadow-[0_2px_5px_rgba(15,23,42,0.03)] sm:p-8"
            aria-labelledby="grade-title"
        >
            <div class="grid gap-8 sm:grid-cols-[230px_1fr] sm:items-end sm:gap-10">
                <div>
                    <h2
                        id="grade-title"
                        class="flex items-center gap-2 text-base font-semibold text-slate-900"
                    >
                        <span class="size-2 rounded-full bg-emerald-400"></span>
                        โอกาสได้เกรด A
                    </h2>

                    <div class="mt-2 flex items-baseline gap-2">
                        <strong class="text-5xl font-bold tracking-tight text-emerald-500">
                            {{ $course['grade_a_chance'] }}%
                        </strong>
                        <span class="text-xs text-slate-500">
                            ของผู้เรียนทั้งหมด
                        </span>
                    </div>

                    <p class="mt-3 max-w-[220px] text-sm leading-6 text-slate-500">
                        สัดส่วนผู้เรียนที่ได้เกรด A โดยประมาณ
                    </p>

                    <p class="mt-4 border-t border-slate-100 pt-3 text-[11px] leading-4 text-slate-400">
                        สุ่มกลุ่มตัวอย่างจากผู้เรียน {{ number_format($course['grade_sample_size']) }} คน · ได้ค่าประมาณ
                    </p>
                </div>

                <div class="flex h-32 items-end justify-between gap-2 sm:gap-4">
                    @foreach ($gradeDistribution as $gradeBar)
                        <x-course.grade-bar
                            :grade="$gradeBar['grade']"
                            :percentage="$gradeBar['percentage']"
                            :bar-height="$gradeBar['barHeight']"
                            :highlighted="$gradeBar['highlighted']"
                        />
                    @endforeach
                </div>
            </div>
        </section>

        <section
            class="mt-8 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8"
            aria-labelledby="overview-title"
        >
            <p class="text-[11px] font-semibold tracking-[0.14em] text-slate-500">
                OVERVIEW
            </p>
            <h2
                id="overview-title"
                class="mt-2 flex items-center gap-3 text-xl font-bold text-slate-950"
            >
                <span class="h-7 w-1 rounded-full bg-[#2f80ed]"></span>
                รายละเอียดรายวิชา
            </h2>

            <p class="mt-6 text-sm leading-7 text-slate-600">
                {{ $course['description'] }}
                ศึกษาแนวคิดพื้นฐานด้านความมั่นคงปลอดภัยไซเบอร์ ภัยคุกคามและรูปแบบการโจมตี
                หลักการเข้ารหัสข้อมูล การป้องกันระบบเครือข่ายและระบบปฏิบัติการ
                การยืนยันตัวตนและการควบคุมการเข้าถึง
                ตลอดจนนโยบายและมาตรฐานความมั่นคงปลอดภัยที่จำเป็นสำหรับสายงานไอที
            </p>

            <div class="my-7 border-t border-slate-200"></div>

            <h3 class="text-sm font-bold text-slate-900">
                หัวข้อหลักที่ได้เรียน
            </h3>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($course['topics'] as $topic)
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs text-slate-600 transition-colors duration-200 hover:border-blue-500 hover:bg-white hover:text-blue-500">
                        <span class="size-1.5 rounded-full bg-[#477ff3]"></span>
                        {{ $topic }}
                    </span>
                @endforeach
            </div>
        </section>

        <section
            class="mt-8 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8"
            aria-labelledby="prerequisites-title"
        >
            <p class="text-[11px] font-semibold tracking-[0.14em] text-slate-500">
                PREREQUISITES
            </p>
            <h2
                id="prerequisites-title"
                class="mt-2 flex items-center gap-3 text-xl font-bold text-slate-950"
            >
                <span class="h-7 w-1 rounded-full bg-[#2f80ed]"></span>
                เงื่อนไขก่อนลงทะเบียน
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                วิชาที่ควรผ่านมาก่อน และวิชาที่เปิดให้ลงต่อได้
            </p>

            <div class="mt-6 grid gap-7 md:grid-cols-2">
                <div>
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900">
                        <i
                            data-lucide="lock"
                            class="size-4 text-slate-500"
                            aria-hidden="true"
                        ></i>
                        ต้องเรียนผ่านมาก่อน
                    </h3>

                    <div class="mt-4 space-y-3">
                        @forelse ($course['prerequisites'] as $item)
                            <div class="flex cursor-pointer items-center justify-between rounded-[16px] border border-slate-200 bg-slate-50 px-3 py-3">
                                <div>
                                    <strong class="block text-sm text-slate-900">
                                        {{ $item['code'] }}
                                    </strong>
                                    <span class="text-xs text-slate-500">
                                        {{ $item['title'] }}
                                    </span>
                                </div>
                                <span class="rounded-full bg-black px-3 py-1 text-[10px] font-semibold text-white">
                                    บังคับ
                                </span>
                            </div>
                        @empty
                            <div class="flex min-h-20 items-center gap-3 rounded-[16px] border border-dashed border-slate-200 bg-slate-50/70 px-4 py-3 text-slate-400">
                                <i
                                    data-lucide="circle-check"
                                    class="size-5 shrink-0 text-emerald-400"
                                    aria-hidden="true"
                                ></i>
                                <p class="text-xs leading-5">
                                    ไม่มีวิชาที่ต้องผ่านก่อนลงทะเบียน
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900">
                        <i
                            data-lucide="lock"
                            class="size-4 text-slate-500"
                            aria-hidden="true"
                        ></i>
                        ปลดล็อควิชาต่อไป
                    </h3>

                    <div class="mt-4 space-y-3">
                        @forelse ($course['next_courses'] as $item)
                            <div class="group flex cursor-pointer items-center justify-between rounded-[16px] border border-slate-200 px-3 py-3 transition-[border-color,box-shadow,transform] duration-500 ease-out hover:-translate-y-0.5 hover:border-blue-500 hover:bg-white hover:shadow-sm">
                                <div>
                                    <strong class="block text-sm text-slate-900 transition-colors duration-200 group-hover:text-blue-500">
                                        {{ $item['code'] }}
                                    </strong>
                                    <span class="text-xs text-slate-500 transition-colors duration-200 group-hover:text-slate-600">
                                        {{ $item['title'] }}
                                    </span>
                                </div>
                                <i
                                    data-lucide="arrow-up-right"
                                    class="size-4 transform-gpu text-slate-500 transition-[color,transform] duration-500 ease-out group-hover:-translate-y-px group-hover:translate-x-px group-hover:text-blue-500"
                                    aria-hidden="true"
                                ></i>
                            </div>
                        @empty
                            <div class="flex min-h-20 items-center gap-3 rounded-[16px] border border-dashed border-slate-200 bg-slate-50/70 px-4 py-3 text-slate-400">
                                <i
                                    data-lucide="book-open"
                                    class="size-5 shrink-0 text-slate-400"
                                    aria-hidden="true"
                                ></i>
                                <p class="text-xs leading-5">
                                    ยังไม่มีวิชาที่เปิดต่อจากรายวิชานี้
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section
            id="comments"
            class="mt-8 rounded-[22px] border border-slate-200 bg-white p-5 sm:p-6"
            aria-labelledby="comments-title"
        >
            <div class="flex items-center justify-between">
                <h2
                    id="comments-title"
                    class="flex items-center gap-2 text-lg font-semibold text-slate-700"
                >
                    <i
                        data-lucide="message-circle"
                        class="size-5"
                        aria-hidden="true"
                    ></i>
                    ความคิดเห็น
                    <span class="text-sm font-normal text-slate-400">
                        ({{ count($comments) }})
                    </span>
                </h2>

                <div
                    class="flex rounded-full border border-slate-200 p-0.5 text-sm"
                    data-comment-sort
                >
                    <button
                        type="button"
                        class="cursor-pointer whitespace-nowrap rounded-full px-3 py-1 text-xs transition-colors sm:px-5 sm:py-1.5 sm:text-sm data-[active=true]:bg-slate-950 data-[active=true]:text-white data-[active=false]:text-slate-500 data-[active=false]:hover:bg-slate-50"
                        aria-pressed="true"
                        data-active="true"
                        data-comment-sort-option="latest"
                    >
                        ล่าสุด
                    </button>
                    <button
                        type="button"
                        class="cursor-pointer whitespace-nowrap rounded-full px-3 py-1 text-xs transition-colors sm:px-5 sm:py-1.5 sm:text-sm data-[active=true]:bg-slate-950 data-[active=true]:text-white data-[active=false]:text-slate-500 data-[active=false]:hover:bg-slate-50"
                        aria-pressed="false"
                        data-active="false"
                        data-comment-sort-option="popular"
                    >
                        ยอดนิยม
                    </button>
                </div>
            </div>

            <form class="mt-6 flex items-start gap-3">
                <div class="size-9 shrink-0 rounded-full bg-slate-200"></div>

                <div class="flex-1">
                    <label
                        for="course-comment"
                        class="sr-only"
                    >
                        แสดงความคิดเห็น
                    </label>
                    <textarea
                        id="course-comment"
                        rows="2"
                        class="w-full resize-none rounded-[14px] border border-slate-200 px-4 py-3 text-sm outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-50"
                        placeholder="แสดงความคิดเห็น..."
                    ></textarea>

                    <div class="mt-3 flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex cursor-pointer items-center gap-1.5 rounded-[12px] bg-[#63b0fa] px-3 py-2 text-xs font-medium text-white hover:bg-[#3f98ee]"
                        >
                            <i
                                data-lucide="send"
                                class="size-3.5"
                                aria-hidden="true"
                            ></i>
                            ส่ง
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-5 space-y-5">
                @foreach ($comments as $comment)
                    <article class="flex gap-3">
                        <div class="size-9 shrink-0 rounded-full bg-slate-200"></div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-medium text-slate-700">
                                    {{ $comment['name'] }}
                                </h3>
                                <span class="rounded bg-rose-100 px-1.5 py-0.5 text-[9px] text-rose-500">
                                    KKU 60
                                </span>
                                <span class="rounded bg-indigo-100 px-1.5 py-0.5 text-[9px] text-indigo-500">
                                    CY
                                </span>
                            </div>

                            <p class="mt-1 text-xs leading-5 text-slate-600">
                                {{ $comment['text'] }}
                            </p>

                            <div class="mt-1 flex gap-3 text-[10px] text-slate-400">
                                <span>
                                    {{ $comment['time'] }}
                                </span>
                                <button
                                    type="button"
                                    class="cursor-pointer"
                                >
                                    ตอบกลับ
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-1 text-slate-400">
                            <button
                                type="button"
                                class="cursor-pointer"
                                aria-label="ถูกใจ"
                            >
                                <i
                                    data-lucide="heart"
                                    class="size-4"
                                    aria-hidden="true"
                                ></i>
                            </button>
                            <span class="text-[10px]">
                                {{ $comment['likes'] }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>

            <button
                type="button"
                class="mx-auto mt-7 flex cursor-pointer items-center gap-2 rounded-[14px] border border-slate-200 px-5 py-2.5 text-xs text-slate-500 hover:bg-slate-50"
            >
                ดูความคิดเห็นเพิ่มเติม
                <i
                    data-lucide="chevron-down"
                    class="size-4"
                    aria-hidden="true"
                ></i>
            </button>
        </section>

        <section
            class="mt-9"
            aria-labelledby="related-title"
        >
            <h2
                id="related-title"
                class="flex items-center gap-3 text-xl font-bold text-slate-950"
            >
                <span class="h-7 w-1 rounded-full bg-[#2f80ed]"></span>
                วิชาที่เกี่ยวข้อง
            </h2>

            <div class="cg course-related-grid mt-5" data-related-courses>
                @foreach ($relatedCourses as $relatedCourse)
                    <x-course.card :course="$relatedCourse" />
                @endforeach
            </div>
        </section>
        @endif
    </main>

    <div id="toast" role="status" aria-live="polite"></div>
    <script type="application/json" data-course-detail-copy>@json($courseDetailCopy)</script>
    <x-footer />
    </div>
@endsection
