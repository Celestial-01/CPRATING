<a
    href="{{ route('admin.courses.edit', $course) }}"
    class="group relative block min-w-0 aspect-[0.78] overflow-hidden rounded-[17px] bg-slate-900 shadow-sm focus:outline-none focus:ring-4 focus:ring-sky-200"
>
    @if ($course->imageUrl())
        <img
            src="{{ $course->imageUrl() }}"
            alt="ภาพประกอบวิชา {{ $course->course_name_th }}"
            class="{{ $imageClasses }}"
            loading="lazy"
        >
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-slate-600 to-slate-800"></div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/35 to-black/20"></div>

    <span class="absolute right-3 top-3 rounded-full bg-white px-3 py-1 text-[11px] font-medium text-[#1684e5] shadow-sm">
        {{ $course->course_code }}
    </span>

    @unless ($course->is_offered)
        <span class="absolute left-3 top-3 rounded-full bg-[#a90000] px-3 py-1 text-[11px] font-medium text-white shadow-sm">
            ยังไม่เปิดสอน
        </span>
    @endunless

    <div class="absolute inset-x-0 bottom-0 p-5 text-white">
        <h3 class="max-w-[13rem] text-lg font-semibold leading-6">
            {{ $course->course_name_th }}
        </h3>

        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-white/90">
            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                <i data-lucide="star" class="size-3 shrink-0 fill-yellow-400 text-yellow-400" aria-hidden="true"></i>
                {{ $course->rating ?? '-' }}
            </span>
            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                <i data-lucide="eye" class="size-3 shrink-0" aria-hidden="true"></i>
                {{ $course->views ?? '-' }} วิว
            </span>
            <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                <i data-lucide="book-open" class="size-3 shrink-0" aria-hidden="true"></i>
                {{ $course->reviews_count ?? '-' }} รีวิว
            </span>
        </div>
    </div>
</a>
