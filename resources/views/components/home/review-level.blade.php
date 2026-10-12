@props(['label', 'score', 'level'])

<div
    class="min-w-0 rounded-2xl bg-[#f1f7ff] px-3 py-2.5"
    role="group"
    aria-label="{{ $label }} {{ $level }} {{ $score }} จาก 5"
>
    <span class="block text-xs text-slate-500">{{ $label }}</span>
    <span class="mt-1.5 flex gap-1" aria-hidden="true">
        @for ($segment = 1; $segment <= 5; $segment++)
            <span class="h-2 min-w-0 flex-1 rounded-full {{ $segment <= $score ? 'bg-[#1684e5]' : 'bg-[#cfe2fa]' }}"></span>
        @endfor
    </span>
    <span class="mt-1 block text-xs font-semibold text-[#1684e5]">{{ $level }}</span>
</div>
