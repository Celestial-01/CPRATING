<header
    id="site-navbar"
    class="{{ $navbarClasses }}"
    @if ($homeHero)
        data-home-navbar
        data-scrolled="false"
        inert
        aria-hidden="true"
    @endif
>
    <nav
        class="flex min-h-[66px] items-center gap-4 rounded-[20px] bg-white px-4 py-3 shadow-[0_8px_18px_rgba(15,23,42,0.14)] sm:gap-6 sm:px-5"
        aria-label="เมนูหลัก"
    >
        <a
            href="{{ route('home') }}"
            class="site-navbar__brand flex shrink-0 items-center gap-2.5 text-sm font-bold leading-none text-[#0b1220]"
            aria-label="CPRATING หน้าหลัก"
            data-home-nav-link
        >
            <span class="site-navbar__mark grid size-9 place-items-center rounded-full bg-[#0a7ae6] text-sm font-bold text-white sm:size-10">CP</span>
            <span class="site-navbar__wordmark inline">CP<span class="text-[#0a7ae6]">RATING</span></span>
        </a>

        <ul class="site-navbar__links hidden items-center gap-1 md:flex" aria-label="ลิงก์หลัก">
            @foreach ($links as $link)
                <li>
                    <a
                        href="{{ route($link['route']) }}"
                        class="{{ $link['desktopClasses'] }}"
                        @if ($link['key'] === 'home') data-home-nav-link @endif
                        @if ($link['isActive']) aria-current="page" @endif
                    >
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="site-navbar__actions ml-auto flex items-center gap-2">
            <button
                type="button"
                class="site-navbar__toggle grid size-10 cursor-pointer place-items-center rounded-full border-0 bg-[#0a7ae6] text-white transition hover:bg-[#0869c7] focus:outline-none focus:ring-4 focus:ring-sky-100 md:hidden"
                aria-label="เปิดเมนู"
                aria-controls="mobile-navigation"
                aria-expanded="false"
                data-mobile-menu-toggle
            >
                <i data-lucide="menu" class="size-5" aria-hidden="true"></i>
            </button>
            @if ($user)
                <div
                    class="site-navbar__profile relative hidden md:block"
                    data-user-menu
                >
                    <button
                        type="button"
                        class="site-navbar__profile-trigger flex cursor-pointer items-center gap-2 rounded-full bg-white px-2 py-1.5 text-left shadow-[0_8px_20px_rgba(15,23,42,0.12)] transition hover:shadow-[0_10px_24px_rgba(15,23,42,0.16)] focus:outline-none focus:ring-4 focus:ring-sky-100"
                        data-user-menu-toggle
                        aria-expanded="false"
                        aria-haspopup="menu"
                    >
                        <span class="relative grid size-9 shrink-0 place-items-center overflow-hidden rounded-full {{ $user->avatarColorClasses() }}">
                            <span class="text-base font-semibold" aria-hidden="true">{{ $user->avatarInitial() }}</span>
                            @if ($user->avatarUrl())
                                <img
                                    src="{{ $user->avatarUrl() }}"
                                    alt="รูปโปรไฟล์ของ {{ $user->displayName() }}"
                                    class="absolute inset-0 size-full object-cover"
                                    referrerpolicy="no-referrer"
                                    data-avatar-image
                                >
                            @endif
                        </span>
                        <span class="max-w-[120px] truncate text-sm font-semibold text-slate-700">
                            {{ $user->firstName() }}
                        </span>
                        <i data-lucide="chevron-down" class="size-4 shrink-0 text-slate-400" aria-hidden="true"></i>
                    </button>

                    <div
                        class="site-navbar__profile-menu absolute right-0 top-[calc(100%+12px)] z-20 hidden w-[300px] overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.18)]"
                        data-user-menu-panel
                        role="menu"
                        aria-hidden="true"
                    >
                        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4">
                            <span class="relative grid size-12 shrink-0 place-items-center overflow-hidden rounded-full {{ $user->avatarColorClasses() }}">
                                <span class="text-lg font-semibold" aria-hidden="true">{{ $user->avatarInitial() }}</span>
                                @if ($user->avatarUrl())
                                    <img
                                        src="{{ $user->avatarUrl() }}"
                                        alt=""
                                        class="absolute inset-0 size-full object-cover"
                                        referrerpolicy="no-referrer"
                                        data-avatar-image
                                    >
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $user->displayName() }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="p-2">
                            @if ($user->isAdmin())
                                <a
                                    href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-[#edf7ff] hover:text-[#0a7ae6]"
                                    role="menuitem"
                                >
                                    <i data-lucide="layout-dashboard" class="size-4" aria-hidden="true"></i>
                                    <span>เข้าสู่หลังบ้าน</span>
                                </a>
                            @endif
                            <a
                                href="{{ route('profile.show') }}"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-[#edf7ff] hover:text-[#0a7ae6]"
                                role="menuitem"
                            >
                                <i data-lucide="user-round" class="size-4" aria-hidden="true"></i>
                                <span>โปรไฟล์</span>
                            </a>
                            <form action="{{ route('auth.logout') }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-red-500 transition hover:bg-red-50"
                                    role="menuitem"
                                >
                                    <i data-lucide="log-out" class="size-4" aria-hidden="true"></i>
                                    <span>ออกจากระบบ</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a
                    href="{{ route('auth.login') }}"
                    class="site-navbar__login hidden rounded-full bg-[#0a7ae6] px-6 py-2.5 text-[15px] font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#0869c7] hover:shadow-[0_12px_24px_-12px_#0a7ae6] focus:outline-none focus:ring-4 focus:ring-sky-100 md:inline-flex"
                >
                    เข้าสู่ระบบ
                </a>
            @endif
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
    class="mobile-menu pointer-events-none fixed inset-y-0 right-0 z-[70] flex w-[min(100%,368px)] flex-col overflow-y-auto rounded-l-[28px] bg-white px-4 pb-5 pt-5 opacity-0 shadow-[-12px_0_32px_rgba(15,23,42,0.14)] transition-[transform,opacity] duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform md:hidden"
    data-mobile-menu
    data-open="false"
    aria-label="เมนูมือถือ"
    aria-hidden="true"
    inert
>
    <div class="mobile-menu__content opacity-0 transition-[transform,opacity] duration-500 ease-out" data-mobile-menu-content>
        <div class="mobile-menu__header flex items-center justify-between border-b border-slate-200 pb-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3" data-mobile-menu-link data-home-nav-link>
                <span class="grid size-10 place-items-center rounded-full bg-[#0a7ae6] text-sm font-bold text-white">CP</span>
                <span class="text-xs font-semibold leading-4 text-slate-700">CP<span class="text-[#0a7ae6]">RATING</span><br><span class="font-normal text-slate-500">Course Rating</span></span>
            </a>
            <button
                type="button"
                class="mobile-menu__close grid size-10 cursor-pointer place-items-center rounded-full border border-slate-200 bg-slate-50 text-slate-600 transition-[background-color,transform] duration-300 ease-out hover:bg-[#dceeff] hover:text-[#0a7ae6] focus:outline-none focus:ring-4 focus:ring-sky-100"
                aria-label="ปิดเมนู"
                data-mobile-menu-close
            >
                <i data-lucide="x" class="size-5" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="mobile-menu__links mt-4 grid overflow-hidden rounded-[18px] border border-slate-200 bg-white" aria-label="เมนูมือถือ">
            @foreach ($links as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="{{ $link['mobileClasses'] }}"
                    data-mobile-menu-link
                    @if ($link['key'] === 'home') data-home-nav-link @endif
                    @if ($link['isActive']) aria-current="page" @endif
                >
                    <span>{{ $link['label'] }}</span>
                    <i data-lucide="chevron-right" class="size-5 shrink-0 text-current" aria-hidden="true"></i>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="mobile-menu__footer mt-auto border-t border-slate-200 pt-4 opacity-0 transition-[transform,opacity] duration-500 ease-out">
        @if ($user)
            <div class="mb-3 overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm">
                <button
                    type="button"
                    class="group flex w-full cursor-pointer items-center gap-3 px-3 py-3 text-left transition-colors hover:bg-slate-50 focus:outline-none focus-visible:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-300"
                    data-mobile-profile-toggle
                    aria-expanded="false"
                    aria-controls="mobile-profile-options"
                >
                    <span class="relative grid size-11 shrink-0 place-items-center overflow-hidden rounded-full {{ $user->avatarColorClasses() }}">
                        <span class="text-base font-semibold" aria-hidden="true">{{ $user->avatarInitial() }}</span>
                        @if ($user->avatarUrl())
                            <img
                                src="{{ $user->avatarUrl() }}"
                                alt="รูปโปรไฟล์ของ {{ $user->displayName() }}"
                                class="absolute inset-0 size-full object-cover"
                                referrerpolicy="no-referrer"
                                data-avatar-image
                            >
                        @endif
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-slate-900">{{ $user->firstName() }}</span>
                        <span class="block truncate text-xs text-slate-500">{{ $user->email }}</span>
                    </span>
                    <i data-lucide="chevron-down" class="size-4 shrink-0 text-slate-500 transition-transform duration-300 group-aria-expanded:rotate-180" aria-hidden="true"></i>
                </button>

                <div
                    id="mobile-profile-options"
                    class="grid grid-rows-[0fr] opacity-0 transition-[grid-template-rows,opacity] duration-300 ease-out data-[open=true]:grid-rows-[1fr] data-[open=true]:opacity-100 motion-reduce:transition-none"
                    data-mobile-profile-panel
                    data-open="false"
                    aria-hidden="true"
                    inert
                >
                    <div class="min-h-0 overflow-hidden">
                        <div class="border-t border-slate-200 p-2">
                            @if ($user->isAdmin())
                                <a
                                    href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 hover:text-[#0a7ae6] focus:outline-none focus-visible:bg-slate-50 focus-visible:ring-2 focus-visible:ring-sky-300"
                                    data-mobile-menu-link
                                >
                                    <i data-lucide="layout-dashboard" class="size-4" aria-hidden="true"></i>
                                    เข้าสู่หลังบ้าน
                                </a>
                            @endif
                            <a
                                href="{{ route('profile.show') }}"
                                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 hover:text-[#0a7ae6] focus:outline-none focus-visible:bg-slate-50 focus-visible:ring-2 focus-visible:ring-sky-300"
                                data-mobile-menu-link
                            >
                                <i data-lucide="user-round" class="size-4" aria-hidden="true"></i>
                                โปรไฟล์
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-full border border-red-200 px-4 py-3 text-[15px] font-semibold text-red-500 transition hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100"
                >
                    <i data-lucide="log-out" class="size-4" aria-hidden="true"></i>
                    ออกจากระบบ
                </button>
            </form>
        @else
            <a
                href="{{ route('auth.login') }}"
                class="mobile-menu__login inline-flex w-full cursor-pointer items-center justify-center rounded-full bg-[#0a7ae6] px-4 py-3 text-[15px] font-semibold text-white shadow-sm transition hover:bg-[#0869c7] focus:outline-none focus:ring-4 focus:ring-sky-100"
                data-mobile-menu-link
            >
                เข้าสู่ระบบ
            </a>
        @endif
    </div>
</aside>
