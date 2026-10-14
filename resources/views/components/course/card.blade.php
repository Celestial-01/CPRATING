@props([
    'course',
    'compact' => false,
    'saveable' => true,
    'history' => false,
])

<article
    class="course-card{{ $compact ? ' course-card--compact' : '' }}"
    data-course-card
    data-course-code="{{ $course->course_code }}"
    @if ($history) data-history-card hidden @endif
>
    @if ($course->imageUrl())
        <img src="{{ $course->imageUrl() }}" alt="ภาพประกอบรายวิชา {{ $course->course_name_th }}" loading="lazy">
    @else
        <div class="absolute inset-0 grid place-items-center bg-[#e9f0f5] text-[#8b9bac]" aria-hidden="true">
            <i data-lucide="book-open" class="size-16"></i>
        </div>
    @endif
    <a
        class="course-card__link"
        href="{{ route('courses.show', $course->course_code) }}"
        data-course-link="{{ $course->course_code }}"
        aria-label="ดูรายละเอียด {{ $course->course_name_th }}"
    ></a>
    <span class="course-card__code">{{ $course->course_code }}</span>

    @if ($saveable)
        <button
            type="button"
            class="course-card__save-button"
            data-save="{{ $course->course_code }}"
            data-course-save="{{ $course->course_code }}"
            aria-pressed="false"
            aria-label="บันทึกวิชา {{ $course->course_code }}"
        >
            <i data-lucide="bookmark" class="size-4" aria-hidden="true"></i>
        </button>
    @endif

    <div class="course-card__info">
        <small>{{ $course->course_name_en ?: $course->course_type }}</small>
        <h3>{{ $course->course_name_th }}</h3>
        <div class="course-card__meta">
            <span>หน่วยกิต {{ $course->credits }}</span>
            <span class="course-card__rating">{{ $course->interestRating() === null ? 'ยังไม่มีคะแนน' : number_format($course->interestRating(), 1) }}</span>
        </div>
        <div class="course-card__details">
            <div>
                <span>ดูรายละเอียด <i><i data-lucide="arrow-up-right" class="size-4" aria-hidden="true"></i></i></span>
            </div>
        </div>
    </div>
</article>
