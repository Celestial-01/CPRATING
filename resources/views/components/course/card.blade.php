<article
    class="{{ $classes }}"
    data-course-card
    data-course-code="{{ $course['code'] }}"
    @if ($history) data-history-card hidden @endif
    data-major="{{ $course['major_code'] }}"
    data-rating="{{ $course['rating'] }}"
    data-search="{{ $searchTerm }}"
>
    <img src="{{ $course['image'] }}" alt="ภาพประกอบรายวิชา {{ $course['title'] }}" loading="lazy">
    <a
        class="lnk"
        href="{{ route('courses.show', $course['code']) }}"
        data-course-link="{{ $course['code'] }}"
        aria-label="ดูรายละเอียด {{ $course['title'] }}"
    ></a>
    <span class="code">{{ $course['code'] }}</span>

    @if ($saveable)
        <button
            type="button"
            class="save"
            data-save="{{ $course['code'] }}"
            data-course-save="{{ $course['code'] }}"
            aria-pressed="false"
            aria-label="บันทึกวิชา {{ $course['code'] }}"
        >
            <i data-lucide="bookmark" class="size-4" aria-hidden="true"></i>
        </button>
    @endif

    <div class="info">
        <small>{{ $course['english_title'] ?? $course['category'] }}</small>
        <h3>{{ $course['title'] }}</h3>
        <div class="meta">
            <span>{{ $course['credits'] }}</span>
            <span class="star">{{ number_format($course['rating'], 1) }}</span>
        </div>
        <div class="more">
            <div>
                <span>ดูรายละเอียด <i><i data-lucide="arrow-up-right" class="size-4" aria-hidden="true"></i></i></span>
            </div>
        </div>
    </div>
</article>
