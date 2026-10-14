@props(['grade', 'percentage', 'count'])

<div
    class="min-w-0 flex-1 text-center"
    data-grade="{{ $grade }}"
    role="group"
    aria-label="เกรด {{ $grade }} จากผู้รายงาน {{ number_format($count) }} คน"
>
    <span class="text-[10px] font-medium {{ $percentage === null ? 'text-slate-500' : 'text-emerald-500' }}">
        {{ $percentage === null ? '—%' : $percentage.'%' }}
    </span>
    <div class="mt-2 flex h-24 items-end justify-center" aria-hidden="true">
        <div
            class="w-full max-w-16 rounded-t-[17px] {{ $percentage === null ? 'min-h-2 bg-slate-100' : 'bg-emerald-500' }}"
            data-progress-height="{{ $percentage ?? 0 }}"
        ></div>
    </div>
    <span class="mt-2 block text-[11px] font-medium text-slate-600">{{ $grade }}</span>
</div>
