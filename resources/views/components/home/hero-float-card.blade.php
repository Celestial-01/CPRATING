@props(['course'])

<div class="hero-float-card" aria-hidden="true">
    @if ($course->imageUrl())
        <img src="{{ $course->imageUrl() }}" alt="">
    @else
        <div class="grid size-full place-items-center bg-[#e9f0f5] text-[#8b9bac]">
            <i data-lucide="book-open" class="size-12"></i>
        </div>
    @endif
    <span>
        {{ $course->course_code }}
        <em>{{ $course->interestRating() === null ? 'ยังไม่มีคะแนน' : '★ '.number_format($course->interestRating(), 1) }}</em>
    </span>
</div>
