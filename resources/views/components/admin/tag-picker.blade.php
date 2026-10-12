@props([
    'name',
    'url',
    'addLabel' => '+ เพิ่ม',
    'placeholder' => 'พิมพ์เพื่อค้นหา',
    'hint' => null,
    'selected' => [],
    'labelKeys' => 'name',
    'subKeys' => '',
    'alwaysOpen' => false,
])

<div
    data-tag-picker
    data-url="{{ $url }}"
    data-name="{{ $name }}"
    data-label-keys="{{ $labelKeys }}"
    data-sub-keys="{{ $subKeys }}"
    data-panel-state="{{ $alwaysOpen ? 'open' : 'closed' }}"
    data-empty="{{ count($selected) ? 'false' : 'true' }}"
    class="group/picker"
>
    <div data-tags class="mb-3 flex flex-wrap gap-2">
        @foreach ($selected as $item)
            <span
                data-tag
                data-id="{{ $item['id'] }}"
                data-label="{{ $item['label'] }}"
                data-sublabel="{{ $item['sublabel'] ?? '' }}"
                class="inline-flex items-center gap-2 rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]"
            >
                <input type="hidden" name="{{ $name }}[]" value="{{ $item['id'] }}">
                <span class="flex flex-col">
                    <span data-slot="label">{{ $item['label'] }}</span>
                    <span data-slot="sublabel" class="text-xs font-normal text-[#5792bd] empty:hidden">{{ $item['sublabel'] ?? '' }}</span>
                </span>
                <button type="button" data-remove aria-label="ลบ" class="text-xl leading-none text-[#1684e5] hover:text-red-500">×</button>
            </span>
        @endforeach
    </div>

    <div class="relative mb-3 hidden group-data-[panel-state=open]/picker:block">
        <input
            data-search
            type="text"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
        >

        <div
            data-results
            data-state="idle"
            class="group/results absolute z-20 mt-1 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg data-[state=empty]:block data-[state=error]:block data-[state=list]:block data-[state=loading]:block"
        >
            <p class="hidden px-4 py-3 text-sm text-slate-400 group-data-[state=loading]/results:block">กำลังค้นหา...</p>
            <p class="hidden px-4 py-3 text-sm text-slate-400 group-data-[state=empty]/results:block">ไม่พบข้อมูลที่เลือกได้</p>
            <p class="hidden px-4 py-3 text-sm text-red-500 group-data-[state=error]/results:block">ค้นหาไม่สำเร็จ กรุณาลองใหม่</p>
            <div data-list></div>
        </div>
    </div>

    @unless ($alwaysOpen)
        <button
            type="button"
            data-toggle
            class="h-10 rounded-[11px] border border-[#bfddfa] px-4 text-sm font-medium text-[#1684e5] transition hover:bg-[#edf7ff]"
        >
            {{ $addLabel }}
        </button>
    @endunless

    @if ($hint)
        <p class="mt-2 hidden text-sm text-slate-400 group-data-[empty=true]/picker:block">{{ $hint }}</p>
    @endif


    <template data-tag-template>
        <span
            data-tag
            class="inline-flex items-center gap-2 rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]"
        >
            <input type="hidden">
            <span class="flex flex-col">
                <span data-slot="label"></span>
                <span data-slot="sublabel" class="text-xs font-normal text-[#5792bd] empty:hidden"></span>
            </span>
            <button type="button" data-remove aria-label="ลบ" class="text-xl leading-none text-[#1684e5] hover:text-red-500">×</button>
        </span>
    </template>

   
    <template data-result-template>
        <button
            type="button"
            data-result
            class="block w-full border-b border-slate-100 px-4 py-2.5 text-left text-sm transition hover:bg-[#edf7ff]"
        >
            <span data-slot="label" class="block font-medium text-slate-700"></span>
            <span data-slot="sublabel" class="block text-xs text-slate-500 empty:hidden"></span>
        </button>
    </template>
</div>
