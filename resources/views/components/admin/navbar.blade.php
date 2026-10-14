<header class="relative z-50 mx-auto w-full max-w-[1120px] bg-white px-6 pb-3 pt-8">
    <nav
        class="flex min-h-[66px] items-center gap-6 rounded-[20px] bg-white px-5 py-3 shadow-[0_8px_18px_rgba(15,23,42,0.14)]"
        aria-label="เมนูผู้ดูแลระบบ"
    >
        <a
            href="{{ route('home') }}"
            class="site-navbar__brand shrink-0"
            aria-label="CPRATING หน้าหลัก"
            data-home-nav-link
        >
            <img
                src="{{ asset('images/cprating-logo.svg') }}"
                alt=""
                class="block h-auto w-[154px] sm:w-[172px]"
                width="307"
                height="80"
            >
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
