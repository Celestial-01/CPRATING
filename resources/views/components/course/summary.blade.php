@props(['course'])

<section
    {{ $attributes->class('@container rounded-[24px] border border-[#ececec] bg-white p-3 md:px-7 md:pt-7 md:pb-7') }}
    aria-labelledby="course-title"
>
    <div class="grid items-stretch gap-7 @min-[640px]:grid-cols-[minmax(0,0.44fr)_minmax(0,1fr)]">
        <div class="mx-auto w-full max-w-none @min-[640px]:mx-0">
            <img
                src="{{ $course['image'] }}"
                alt="ภาพประกอบวิชา {{ $course['title'] }}"
                width="300"
                height="313"
                class="aspect-[300/313] w-full rounded-[16px] object-cover object-center"
            >

            <dl class="mt-6 grid grid-cols-3 gap-2.5">
                <x-course.stat
                    :value="$course['rating']"
                    label="คะแนน"
                    tone="rating"
                />
                <x-course.stat
                    :value="$course['learners']"
                    label="ผู้เข้าชม"
                    tone="views"
                />
                <x-course.stat
                    :value="$course['views']"
                    label="รีวิว"
                />
            </dl>
        </div>

        <div class="flex min-w-0 flex-col">
            <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-medium leading-4">
                <span class="rounded-full bg-[#eef5ff] px-3 py-1 text-[11px] leading-4 text-[#2b7fff]">
                    {{ $course['code'] }}
                </span>
                <span class="rounded-full bg-[#f5f5f5] px-3 py-1 text-[11px] leading-4 text-[#888888]">
                    {{ $course['categoryCode'] ?? $course['category'] }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-[#f1f5f9] px-3 py-1 text-[11px] leading-4 text-[#718096]">
                    <i
                        data-lucide="book-open"
                        class="size-3.5"
                        aria-hidden="true"
                    ></i>
                    {{ $course['credits'] }}
                </span>
            </div>

            <h1
                id="course-title"
                class="mt-4 text-[clamp(22px,3.16cqw,26px)] font-bold leading-[1.35] text-black"
            >
                {{ $course['title'] }}
            </h1>
            <p class="mt-1 text-[15px] font-bold leading-6 text-[#7c8ca4]">
                {{ $course['englishTitle'] }}
            </p>
            <p class="mt-2 flex items-center gap-1.5 text-xs text-[#7c8ca4]">
                <i
                    data-lucide="graduation-cap"
                    class="size-3.5 shrink-0"
                    aria-hidden="true"
                ></i>
                ผู้สอน: {{ $course['instructor'] }}
            </p>

            <div class="mt-5 rounded-[16px] border-2 border-[#edf1f6] bg-[#f8fafc] px-4 py-3">
                <p class="text-sm font-medium leading-[1.75] text-[#5e6d84] @min-[640px]:text-[13px]">
                    {{ $course['description'] }}
                </p>
                <p class="mt-1.5 flex items-center gap-1.5 text-[11px] leading-4 text-[#8a93a1] @min-[640px]:text-[10px]">
                    <i
                        data-lucide="clock"
                        class="size-3 shrink-0"
                        aria-hidden="true"
                    ></i>
                    อัปเดตเมื่อ 4 เดือนที่แล้ว
                </p>
            </div>

            <div class="mt-auto grid grid-cols-1 gap-3.5 pt-5 @min-[640px]:grid-cols-[minmax(0,1fr)_minmax(0,0.4fr)_minmax(0,0.4fr)]">
                <button
                    type="button"
                    class="inline-flex h-12 cursor-pointer items-center justify-center gap-2 rounded-[18px] bg-[#2b7fff] px-3 text-[15px] font-semibold text-white transition hover:bg-[#2573e8] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100"
                >
                    <i
                        data-lucide="star"
                        class="size-4 shrink-0"
                        aria-hidden="true"
                    ></i>
                    ให้คะแนนวิชานี้
                </button>
                <button
                    type="button"
                    class="inline-flex h-12 cursor-pointer items-center justify-center gap-2 rounded-[18px] border border-[#cccccc] px-2 text-sm text-[#636363] transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100 @min-[640px]:flex-col @min-[640px]:gap-1 @min-[700px]:flex-row @min-[700px]:gap-2"
                >
                    <i
                        data-lucide="bookmark"
                        class="size-4 shrink-0"
                        aria-hidden="true"
                    ></i>
                    บันทึกเก็บไว้
                </button>
                <a
                    href="#comments"
                    class="inline-flex h-12 cursor-pointer items-center justify-center gap-2 rounded-[18px] border border-[#cccccc] px-2 text-sm text-[#636363] transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100 @min-[640px]:flex-col @min-[640px]:gap-1 @min-[700px]:flex-row @min-[700px]:gap-2"
                >
                    <i
                        data-lucide="message-circle"
                        class="size-4 shrink-0"
                        aria-hidden="true"
                    ></i>
                    คอมเมนต์
                </a>
            </div>
        </div>
    </div>
</section>
