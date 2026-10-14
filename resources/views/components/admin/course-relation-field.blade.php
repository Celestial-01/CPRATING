@props([
    'course',
    'field',
    'title',
    'emptyMessage',
    'selected',
    'searchUrl',
    'placeholder',
    'labelKeys',
    'selectedLabel',
    'addLabel' => '+ เพิ่ม',
    'subKeys' => '',
    'selectedSubLabel' => null,
    'alwaysOpen' => false,
    'wide' => false,
])

<section
    data-inline-relation
    data-field="{{ $field }}"
    data-url="{{ route('admin.courses.inline-update', $course) }}"
    data-editing="false"
    data-has-error="false"
    data-saving="false"
    data-empty="{{ count($selected) ? 'false' : 'true' }}"
    class="group/relation rounded-[22px] border border-slate-200 bg-white p-6 {{ $wide ? 'md:col-span-2' : '' }}"
>
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">{{ $title }}</h2>
        <button
            type="button"
            data-edit
            class="rounded-full bg-sky-50 px-4 py-1.5 text-sm font-medium text-[#1684e5] transition hover:bg-[#d4ebff] group-data-[editing=true]/relation:hidden"
        >
            แก้ไข
        </button>
    </div>

    <div data-view class="mt-4 group-data-[editing=true]/relation:hidden">
        <div data-chips class="flex flex-wrap gap-2">
            @foreach ($selected as $item)
                <span data-chip class="inline-flex items-center gap-1.5 rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]">
                    <span data-slot="label">{{ data_get($item, $selectedLabel) }}</span>
                    <span data-slot="sublabel" class="font-normal empty:hidden">{{ $selectedSubLabel ? data_get($item, $selectedSubLabel) : '' }}</span>
                </span>
            @endforeach

            <p class="hidden text-sm text-slate-400 group-data-[empty=true]/relation:block">{{ $emptyMessage }}</p>
        </div>
    </div>

    <div data-editor class="mt-4 hidden group-data-[editing=true]/relation:block">
        <x-admin.tag-picker
            :name="$field"
            :url="$searchUrl"
            :add-label="$addLabel"
            :placeholder="$placeholder"
            :label-keys="$labelKeys"
            :sub-keys="$subKeys"
            :always-open="$alwaysOpen"
            :selected="$selected"
            :selected-label="$selectedLabel"
            :selected-sub-label="$selectedSubLabel"
        />

        <p data-error class="mt-2 hidden text-sm text-red-600 group-data-[has-error=true]/relation:block"></p>

        <div class="mt-4 flex gap-2">
            <button
                type="button"
                data-save
                class="h-9 rounded-full bg-[#1684e5] px-5 text-sm font-medium text-white transition hover:bg-[#0e73cc] disabled:opacity-60"
            >
                <span class="group-data-[saving=true]/relation:hidden">บันทึก</span>
                <span class="hidden group-data-[saving=true]/relation:inline">กำลังบันทึก...</span>
            </button>
            <button
                type="button"
                data-cancel
                class="h-9 rounded-full border border-slate-200 px-5 text-sm text-slate-600 transition hover:bg-slate-50"
            >
                ยกเลิก
            </button>
        </div>
    </div>

    <template data-chip-template>
        <span data-chip class="inline-flex items-center gap-1.5 rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]">
            <span data-slot="label"></span>
            <span data-slot="sublabel" class="font-normal empty:hidden"></span>
        </span>
    </template>
</section>
