<article class="rv">
    <div class="who">
        <i>{{ $review['initial'] }}</i>
        <div>
            <b>{{ $review['year'] }}</b>
            <span>{{ $review['course'] }}</span>
        </div>
    </div>

    <div class="stars" aria-label="ความน่าสนใจ {{ $review['rating'] }} จาก 5">
        <span aria-hidden="true">★★★★★</span> <em aria-hidden="true">{{ $review['rating'] }}</em>
    </div>

    <p>{{ $review['text'] }}</p>

    <div class="grid grid-cols-2 gap-2">
        <x-home.review-level label="ภาระงาน" :score="$review['workload']['score']" :level="$review['workload']['level']" />
        <x-home.review-level label="ความยาก" :score="$review['difficulty']['score']" :level="$review['difficulty']['level']" />
    </div>

    <div class="ft">
        <div class="tg">
            @foreach ($review['tags'] as $tag)
                <span>{{ $tag }}</span>
            @endforeach
        </div>
    </div>
</article>
