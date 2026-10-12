@props([
    'action',
    'currentMajor',
    'types',
    'currentTypes' => [],
    'keyword' => '',
    'placeholder' => 'ค้นหา',
])

<form action="{{ $action }}" method="GET" role="search" class="mt-6">
    <input type="hidden" name="major" value="{{ $currentMajor }}">

    {{-- checkbox ซ่อนไว้ ใช้เป็นสวิตช์เปิด/ปิดแผง (ต้องอยู่ก่อนแผงในไฟล์) --}}
    <input
        type="checkbox"
        id="filter-toggle"
        class="peer/filter sr-only"
        @checked(count($currentTypes) > 0)
    >

    <div class="flex items-center gap-3">
        <input
            type="search"
            name="q"
            value="{{ $keyword }}"
            placeholder="{{ $placeholder }}"
            class="h-10 w-full max-w-[260px] rounded-full bg-[#e5e7eb] px-4 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 focus:ring-sky-100"
        >

        <label
            for="filter-toggle"
            class="inline-flex h-10 cursor-pointer items-center rounded-full bg-[#bfe3ff] px-5 text-sm font-medium text-slate-800 transition hover:bg-[#a8d8ff]"
        >
            คัดกรอง
        </label>
    </div>

    {{-- แผงตัวกรอง: ซ่อนไว้ จนกว่า checkbox จะถูกติ๊ก --}}
    <div class="mt-4 hidden rounded-[24px] bg-white p-6 shadow-[0_8px_18px_rgba(15,23,42,0.10)] peer-checked/filter:block">
        <fieldset>
            <legend class="text-lg font-semibold text-slate-900">ประเภท</legend>
            <div class="mt-3 flex flex-wrap gap-3">
                @foreach ($types as $type)
                    <label class="cursor-pointer">
                        <input
                            type="checkbox"
                            name="types[]"
                            value="{{ $type }}"
                            class="peer sr-only"
                            @checked(in_array($type, $currentTypes, true))
                        >
                        <span class="inline-flex h-9 items-center rounded-full bg-[#e9f5ff] px-5 text-sm font-semibold text-[#1684e5] transition hover:bg-[#d4ebff] peer-checked:bg-[#1684e5] peer-checked:text-white peer-focus-visible:ring-4 peer-focus-visible:ring-sky-200">
                            {{ $type }}
                        </span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <div class="mt-6 flex items-center gap-3">
            <button
                type="submit"
                class="h-10 rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-6 text-sm text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
            >
                ใช้ตัวกรอง
            </button>
            <a
                href="{{ route('admin.dashboard', ['major' => $currentMajor]) }}"
                class="text-sm text-slate-500 transition hover:text-[#1684e5]"
            >
                ล้างตัวกรอง
            </a>
        </div>
    </div>
</form>
