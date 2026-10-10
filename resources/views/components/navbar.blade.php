@props(['active' => 'home', 'homeHero' => false])

<header
    @class([
        'sticky top-0 z-50 mx-auto w-full max-w-[1120px] bg-[#f6f6f6] px-3 pb-2 pt-4 sm:px-6 sm:pb-3 sm:pt-8' => ! $homeHero,
        'home-navbar fixed inset-x-0 top-0 z-50 max-w-none -translate-y-full bg-transparent px-3 pb-2 pt-4 opacity-0 transition-[translate,opacity] duration-500 ease-out pointer-events-none sm:px-6 sm:pb-3 sm:pt-8' => $homeHero,
    ])
    @if ($homeHero)
        data-home-navbar
        inert
    @endif
>
    <nav
        class="mx-auto flex min-h-[66px] w-full max-w-[1160px] items-center gap-6 rounded-[20px] bg-white/95 px-4 py-3 shadow-[0_8px_18px_rgba(15,23,42,0.14)] backdrop-blur sm:px-5"
        aria-label="เมนูหลัก"
    >
        <a
            href="{{ route('home') }}"
            class="flex shrink-0 items-center gap-3 text-sm font-medium text-slate-900"
            aria-label="CPRATING หน้าหลัก"
        >
            <span
                class="grid size-9 place-items-center rounded-xl bg-[#1684e5] text-sm font-semibold text-white sm:size-10"
            >CP</span>
            <span class="hidden font-semibold tracking-tight sm:inline">
                CP<span class="text-[#1684e5]">RATING</span>
            </span>
        </a>

        <div class="hidden items-center gap-7 text-sm md:flex">
            <a href="{{ route('home') }}" @class([
                'border-b-2 border-[#1684e5] pb-1 text-[#1684e5]' => $active === 'home',
                'text-slate-900 transition hover:text-[#1684e5]' => $active !== 'home',
            ])>หน้าหลัก</a>
            <a href="#" class="text-slate-900 transition hover:text-[#1684e5]">ค้นหาวิชา</a>
            <a href="#" class="text-slate-900 transition hover:text-[#1684e5]">แผนการเรียน</a>
            <a href="#" class="text-slate-900 transition hover:text-[#1684e5]">ประวัติ</a>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <button
                type="button"
                class="grid size-10 cursor-pointer place-items-center rounded-full text-slate-500 transition hover:bg-slate-100 md:hidden"
                aria-label="เปิดเมนู"
                aria-controls="mobile-navigation"
                aria-expanded="false"
                data-mobile-menu-toggle
            >
                <i
                    data-lucide="menu"
                    class="size-5 transition-[rotate] duration-500 ease-out"
                    aria-hidden="true"
                    data-mobile-menu-toggle-icon
                ></i>
            </button>
            <a
                href="{{ route('auth.login') }}"
                class="hidden rounded-[14px] bg-[#1684e5] px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-[#0e73cc] focus:outline-none focus:ring-4 focus:ring-sky-100 md:inline-flex"
            >เข้าสู่ระบบ</a>
        </div>
    </nav>
</header>

<div
    class="mobile-menu-overlay pointer-events-none fixed inset-0 z-[60] bg-slate-950/35 opacity-0 transition-opacity duration-300 ease-out md:hidden"
    data-mobile-menu-overlay
    data-open="false"
    aria-hidden="true"
></div>

<aside
    id="mobile-navigation"
    class="mobile-menu pointer-events-none fixed inset-y-0 right-0 z-[70] flex w-[min(100%,368px)] translate-x-full flex-col rounded-l-[24px] bg-white px-4 pb-5 pt-5 opacity-0 shadow-[-12px_0_32px_rgba(15,23,42,0.14)] transition-[translate,opacity] duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-[translate] md:hidden"
    data-mobile-menu
    data-open="false"
    aria-label="เมนูมือถือ"
    inert
>
    <div
        class="translate-y-3 opacity-0 transition-[translate,opacity] duration-500 ease-out"
        data-mobile-menu-content
    >
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3" data-mobile-menu-link>
                <span
                    class="grid size-10 place-items-center rounded-xl bg-[#1684e5] text-sm font-semibold text-white"
                >CP</span>
                <span class="text-xs font-semibold leading-4 text-slate-700">CPRATING<br><span class="font-normal text-slate-500">Course Rating</span></span>
            </a>
            <button
                type="button"
                class="grid size-10 cursor-pointer place-items-center rounded-full border border-slate-200 bg-slate-50 text-slate-600 transition-[background-color,transform] duration-300 ease-out hover:bg-slate-100"
                aria-label="ปิดเมนู"
                data-mobile-menu-close
            >
                <i
                    data-lucide="x"
                    class="size-5 transition-[rotate] duration-300 ease-out"
                    aria-hidden="true"
                    data-mobile-menu-close-icon
                ></i>
            </button>
        </div>

        <nav
            class="mt-4 overflow-hidden rounded-[18px] border border-slate-200 bg-white shadow-sm"
            aria-label="เมนูมือถือ"
        >
            <a
                href="{{ route('home') }}"
                class="flex cursor-pointer items-center justify-between bg-sky-50 px-4 py-4 text-sm font-semibold text-[#1684e5]"
                data-mobile-menu-link
            >
                <span>หน้าหลัก</span>
                <i data-lucide="chevron-right" class="size-5" aria-hidden="true"></i>
            </a>
            <a
                href="#"
                class="flex cursor-pointer items-center justify-between border-t border-slate-200 px-4 py-4 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"
                data-mobile-menu-link
            >
                <span>ค้นหาวิชา</span>
                <i data-lucide="chevron-right" class="size-5 text-slate-400" aria-hidden="true"></i>
            </a>
            <a
                href="#"
                class="flex cursor-pointer items-center justify-between border-t border-slate-200 px-4 py-4 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"
                data-mobile-menu-link
            >
                <span>แผนการเรียน</span>
                <i data-lucide="chevron-right" class="size-5 text-slate-400" aria-hidden="true"></i>
            </a>
            <a
                href="#"
                class="flex cursor-pointer items-center justify-between border-t border-slate-200 px-4 py-4 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"
                data-mobile-menu-link
            >
                <span>ประวัติ</span>
                <i data-lucide="chevron-right" class="size-5 text-slate-400" aria-hidden="true"></i>
            </a>
        </nav>
    </div>

    <div
        class="mt-auto translate-y-3 border-t border-slate-200 pt-4 opacity-0 transition-[translate,opacity] duration-500 ease-out"
        data-mobile-menu-content
    >
        <a
            href="{{ route('auth.login') }}"
            class="inline-flex w-full cursor-pointer items-center justify-center rounded-[14px] bg-[#1684e5] px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-[#0e73cc] focus:outline-none focus:ring-4 focus:ring-sky-100"
            data-mobile-menu-link
        >เข้าสู่ระบบ</a>
    </div>
</aside>
