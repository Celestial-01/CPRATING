@props(['flush' => false])

<footer @class([
    'border-t border-slate-200 bg-white px-6 py-12 text-slate-600 sm:py-14',
    'mt-20 sm:mt-24' => ! $flush,
])>
    <div
        class="mx-auto flex max-w-[1160px] flex-col gap-7 sm:flex-row sm:items-end sm:justify-between"
    >
        <div>
            <a
                href="{{ route('home') }}"
                class="text-lg font-semibold tracking-tight text-slate-950"
            >CP<span class="text-[#1684e5]">RATING</span></a>
            <p class="mt-2 max-w-sm text-sm leading-6">
                พื้นที่แบ่งปันข้อมูลรายวิชา เพื่อการตัดสินใจที่มั่นใจขึ้น
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
            <a
                href="{{ route('home') }}"
                class="transition-colors hover:text-[#1684e5]"
            >หน้าหลัก</a>
            <a
                href="{{ route('auth.login') }}"
                class="transition-colors hover:text-[#1684e5]"
            >เข้าสู่ระบบ</a>
        </div>
    </div>
    <div
        class="mx-auto mt-9 max-w-[1160px] border-t border-slate-100 pt-5 text-xs text-slate-400"
    >
        © {{ date('Y') }} CPRATING
    </div>
</footer>
