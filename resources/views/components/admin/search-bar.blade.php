@props([
    'action',
    'value' => '',
    'placeholder' => 'ค้นหา',
    'button' => 'คัดกรอง',
])

<form action="{{ $action }}" method="GET" role="search" class="mt-6 flex items-center gap-3">
    {{ $slot }}

    <input
        type="search"
        name="q"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="h-10 w-full max-w-[260px] rounded-full bg-[#e5e7eb] px-4 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 focus:ring-sky-100"
    >
    <button
        type="submit"
        class="h-10 rounded-full bg-[#bfe3ff] px-5 text-sm font-medium text-slate-800 transition hover:bg-[#a8d8ff] focus:outline-none focus:ring-4 focus:ring-sky-100"
    >
        {{ $button }}
    </button>
</form>