@props(['course', 'comments', 'sort' => 'latest'])

<section id="comments" class="mt-8 rounded-[22px] border border-slate-200 bg-white p-5 sm:p-6" aria-labelledby="comments-title">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 id="comments-title" class="flex items-center gap-2 text-lg font-semibold text-slate-900">
            <i data-lucide="message-circle" class="size-5 text-[#1684e5]" aria-hidden="true"></i>
            คอมเมนต์ <span class="text-sm font-normal text-slate-400">({{ number_format($comments->count()) }})</span>
        </h2>

        <div class="inline-flex rounded-full bg-slate-100 p-1 text-sm">
            @foreach (['latest' => 'ล่าสุด', 'popular' => 'ยอดนิยม'] as $value => $label)
                <a
                    href="{{ route('admin.courses.edit', ['course' => $course, 'sort' => $value]) }}#comments"
                    data-active="{{ $sort === $value ? 'true' : 'false' }}"
                    class="cursor-pointer rounded-full px-4 py-1.5 text-slate-500 transition data-[active=true]:bg-white data-[active=true]:font-medium data-[active=true]:text-[#1684e5] data-[active=true]:shadow-sm"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($comments as $comment)
            <article class="rounded-[14px] bg-slate-50 px-4 py-4">
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                    <strong class="text-slate-700">{{ $comment->user?->displayName() ?? 'ไม่ทราบชื่อผู้ใช้' }}</strong>
                    <time datetime="{{ $comment->created_at->toIso8601String() }}">
                        {{ $comment->created_at->format('d/m/Y H:i') }}
                    </time>
                    <span>{{ number_format($comment->likes_count) }} ถูกใจ</span>

                    @if ($comment->status !== \App\Models\Comment::STATUS_APPROVED)
                        <span class="text-amber-700">ไม่แสดงบนหน้าเว็บ</span>
                    @endif
                </div>
                <p class="mt-2 whitespace-pre-line break-words text-sm text-slate-700">{{ $comment->content }}</p>
            </article>
        @empty
            <p class="rounded-[14px] bg-slate-50 px-4 py-10 text-center text-sm text-slate-400">
                ยังไม่มีคอมเมนต์
            </p>
        @endforelse
    </div>
</section>
