<div
    data-inline
    data-field="{{ $field }}"
    data-url="{{ route('admin.courses.inline-update', $course) }}"
    data-editing="false"
    data-has-error="false"
    data-saving="false"
    {{ $attributes->merge(['class' => 'group/inline']) }}
>
    @if ($label)
        <p class="mb-1 text-xs text-slate-400">{{ $label }}</p>
    @endif

    <div data-view class="flex items-start gap-2 group-data-[editing=true]/inline:hidden">
        <span data-display data-placeholder="{{ $placeholder }}" class="min-w-0 whitespace-pre-line break-words {{ $displayClass }}">{{ filled($shown) ? $shown : $placeholder }}</span>
        @if ($suffix)
            <span class="text-sm text-slate-500">{{ $suffix }}</span>
        @endif
        <button
            type="button"
            data-edit
            aria-label="แก้ไข"
            title="แก้ไข"
            class="mt-0.5 shrink-0 rounded-full p-1 text-slate-400 transition hover:bg-sky-50 hover:text-[#1684e5]"
        >
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
        </button>
    </div>

    <div data-editor class="hidden group-data-[editing=true]/inline:block">
        @if ($type === 'textarea')
            <textarea data-input rows="5" class="{{ $controlClasses }} py-3">{{ $value }}</textarea>
        @elseif ($type === 'select')
            <select data-input class="{{ $controlClasses }} h-[45px]">
                @foreach ($options as $key => $text)
                    <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $text }}</option>
                @endforeach
            </select>
        @else
            <input data-input type="{{ $type }}" value="{{ $value }}" @if ($type === 'number') min="0" @endif class="{{ $controlClasses }} h-[45px]">
        @endif

        <p data-error class="mt-2 hidden text-sm text-red-600 group-data-[has-error=true]/inline:block"></p>

        <div class="mt-2 flex gap-2">
            <button type="button" data-save class="h-9 rounded-full bg-[#1684e5] px-5 text-sm font-medium text-white transition hover:bg-[#0e73cc] disabled:opacity-60">
                <span class="group-data-[saving=true]/inline:hidden">บันทึก</span>
                <span class="hidden group-data-[saving=true]/inline:inline">กำลังบันทึก...</span>
            </button>
            <button type="button" data-cancel class="h-9 rounded-full border border-slate-200 px-5 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50">ยกเลิก</button>
        </div>
    </div>
</div>
