<header class="relative z-50 mx-auto w-full max-w-[1120px] bg-white px-6 pb-3 pt-8">
    <nav
        class="flex min-h-[66px] items-center gap-6 rounded-[20px] bg-white px-5 py-3 shadow-[0_8px_18px_rgba(15,23,42,0.14)]"
        aria-label="เมนูผู้ดูแลระบบ"
    >
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex shrink-0 items-center gap-3 text-sm font-medium text-slate-900"
            aria-label="CPRATING Admin"
        >
            <span class="grid size-10 place-items-center rounded-full bg-[#d9d9d9] text-[10px] text-slate-700">LOGO</span>
        </a>

        <div class="flex min-w-0 items-center gap-5 overflow-x-auto whitespace-nowrap text-sm lg:gap-7">
            @foreach ($menus as $menu)
                <a
                    href="{{ route($menu['route']) }}"
                    class="{{ $menu['classes'] }}"
                    @if ($menu['active']) aria-current="page" @endif
                >
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </div>

        <span class="ml-auto shrink-0 whitespace-nowrap rounded-full bg-sky-50 px-3 py-1 text-[11px] font-medium text-[#1684e5]">
            ผู้ดูแลระบบ
        </span>
    </nav>
</header>
