<article class="review-card">
    <div class="review-author">
        <i aria-hidden="true">ผ</i>
        <div>
            <b>ผู้เรียน</b>
            <a href="{{ route('courses.show', $review->course_code) }}">
                {{ $review->course_code }} {{ $review->course?->course_name_th }}
            </a>
        </div>
    </div>

    <p>{{ $review->content }}</p>
    <div class="review-card__footer">{{ $review->created_at->diffForHumans() }}</div>
</article>
