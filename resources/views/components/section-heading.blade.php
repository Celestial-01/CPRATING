@props(['icon' => 'flame', 'title', 'href' => '#'])

<div class="mb-7 flex items-center justify-between gap-4">
    <h2 {{ $attributes->class('flex items-center gap-2 text-xl font-semibold leading-8 text-slate-900 sm:text-2xl') }}>
        <i
            data-lucide="{{ $icon }}"
            class="size-6 text-[#1684e5]"
            aria-hidden="true"
        ></i>
        <span>
            {{ $title }}
        </span>
    </h2>

    <a
        href="{{ $href }}"
        class="shrink-0 text-sm text-slate-800 transition hover:text-[#1684e5]"
    >
        ดูทั้งหมด
        <span aria-hidden="true">→</span>
    </a>
</div>
