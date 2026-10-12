@props([
    'action',
    'method' => 'POST',
    'files' => false,
    'cancel',
    'submit' => 'บันทึก',
])

<form
    action="{{ $action }}"
    method="POST"
    @if ($files) enctype="multipart/form-data" @endif
    class="space-y-6 rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    {{ $slot }}

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-6">
        <a
            href="{{ $cancel }}"
            class="inline-flex h-[45px] items-center rounded-[15px] border border-[#bcbcbc] px-6 text-sm text-slate-600 transition hover:bg-slate-50"
        >
            ยกเลิก
        </a>
        <button
            type="submit"
            class="h-[45px] rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-6 text-sm text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
        >
            {{ $submit }}
        </button>
    </div>
</form>
