@props([
    'name',
    'url',
    'addLabel' => '+ เพิ่ม',
    'placeholder' => 'พิมพ์เพื่อค้นหา',
    'hint' => null,
    'selected' => [],
])

<div data-tag-picker data-url="{{ $url }}" data-name="{{ $name }}">
    <div data-tags class="mb-3 flex flex-wrap gap-2">
        @foreach ($selected as $item)
            <span data-tag data-id="{{ $item['id'] }}" class="inline-flex items-center gap-2 rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]">
                <input type="hidden" name="{{ $name }}[]" value="{{ $item['id'] }}">
                <span>{{ $item['label'] }}</span>
                <button type="button" data-remove class="text-[#1684e5] hover:text-red-500" aria-label="ลบ">×</button>
            </span>
        @endforeach
    </div>

    <div data-panel class="relative mb-3 hidden">
        <input
            data-search
            type="text"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
        >
        <div data-results class="absolute z-10 mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg"></div>
    </div>

    <button
        type="button"
        data-toggle
        class="h-10 rounded-[11px] border border-[#bfddfa] px-4 text-sm font-medium text-[#1684e5] transition hover:bg-[#edf7ff]"
    >
        {{ $addLabel }}
    </button>

    @if ($hint)
        <p data-empty class="mt-2 text-sm text-slate-400 {{ count($selected) ? 'hidden' : '' }}">{{ $hint }}</p>
    @endif
</div>

