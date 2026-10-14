@props([
    'grade',
    'percentage',
    'barHeight',
    'highlighted' => false,
])

<div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
    <span class="text-[10px] font-medium text-slate-500">{{ $percentage }}%</span>
    <div
        class="w-full max-w-16 rounded-t-[17px] {{ $highlighted ? 'bg-emerald-500' : 'bg-emerald-100' }}"
        data-progress-height="{{ $barHeight }}"
    ></div>
    <span class="text-[10px] text-slate-500">{{ $grade }}</span>
</div>
