@props(['course'])

<a
    href="{{ route('courses.show', $course['code']) }}"
    class="group relative block min-w-0 aspect-[0.78] overflow-hidden rounded-[20px] bg-slate-900
        shadow-[0_12px_28px_rgba(15,23,42,0.08)] transition-[transform,box-shadow] duration-500
        hover:-translate-y-2 hover:shadow-[0_24px_42px_rgba(15,23,42,0.18)]
        focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-sky-200 motion-reduce:transition-none"
>
    <img
        src="{{ $course['image'] }}"
        alt="ภาพประกอบวิชา {{ $course['title'] }}"
        @class([
            'absolute inset-0 size-full object-cover transition duration-300 group-hover:scale-105',
            'brightness-[0.8]' => $course['isAvailable'],
            'brightness-[0.55]' => ! $course['isAvailable'],
        ])
        loading="lazy"
    >
    <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/35 to-black/20"></div>

    <span
        @class([
            'absolute right-3 top-3 rounded-full bg-white px-3 py-1 text-[11px] font-medium text-[#1684e5] shadow-sm',
            'opacity-70' => ! $course['isAvailable'],
        ])
    >
        {{ $course['code'] }}
    </span>

    @unless ($course['isAvailable'])
        <div class="absolute inset-0 flex items-center justify-center bg-slate-950/20">
            <span class="rounded-full bg-[#a90000] px-5 py-3 text-sm font-semibold text-white shadow-lg sm:text-base">
                ยังไม่เปิดสอน
            </span>
        </div>
    @endunless

    <div
        @class([
            'absolute inset-x-0 bottom-0 p-4 text-white sm:p-5',
            'opacity-65' => ! $course['isAvailable'],
        ])
    >
        <h3 class="max-w-[13rem] text-base font-semibold leading-5 sm:text-lg sm:leading-6">
            {{ $course['title'] }}
        </h3>

        <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[10px] text-white/90 sm:gap-x-4 sm:text-xs">
            <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap">
                <i
                    data-lucide="star"
                    class="size-3 shrink-0 fill-yellow-400 text-yellow-400"
                    aria-hidden="true"
                ></i>
                {{ $course['rating'] }}
            </span>
            <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap">
                <i
                    data-lucide="eye"
                    class="size-3 shrink-0"
                    aria-hidden="true"
                ></i>
                {{ $course['learners'] }} วิว
            </span>
            <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap">
                <i
                    data-lucide="book-open"
                    class="size-3 shrink-0"
                    aria-hidden="true"
                ></i>
                {{ $course['lessons'] }} วิชา
            </span>
        </div>
    </div>
</a>
