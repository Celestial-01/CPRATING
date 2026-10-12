<article class="rounded-[21px] border border-slate-200 bg-white p-5 shadow-[0_2px_5px_rgba(15,23,42,0.02)] sm:p-6">
    <h2 class="flex items-center gap-2 text-sm font-bold text-slate-950">
        <span class="size-1.5 rounded-full {{ $accentClass }}"></span>
        {{ $stat['title'] }}
    </h2>

    <div class="mt-6 flex items-center justify-between text-xs">
        <span class="text-slate-500">{{ $stat['summary'] }}</span>
        <strong class="text-slate-900">{{ $stat['percentage'] === null ? '—' : $stat['percentage'].'%' }}</strong>
    </div>

    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-200">
        <div
            class="h-full rounded-full {{ $accentClass }}"
            data-progress-width="{{ $stat['percentage'] ?? 0 }}"
        ></div>
    </div>

    <p class="mt-2 text-[11px] text-slate-400">{{ $stat['description'] }}</p>
</article>
