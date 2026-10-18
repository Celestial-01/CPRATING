<header class="sticky top-0 z-50 mx-auto w-full max-w-[1120px] bg-white px-3 pb-3 pt-8 sm:px-6">
    <nav
        class="flex min-h-[66px] items-center gap-3 rounded-[20px] bg-white px-3 py-3 shadow-[0_8px_18px_rgba(15,23,42,0.14)] sm:gap-6 sm:px-5"
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

        @if ($user)
            <div class="relative ml-auto shrink-0" data-user-menu>
                <button
                    type="button"
                    class="group flex cursor-pointer items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-left transition hover:border-sky-200 hover:bg-sky-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-sky-100"
                    data-user-menu-toggle
                    aria-label="โปรไฟล์ของ {{ $user->displayName() }}"
                    aria-controls="admin-profile-menu"
                    aria-expanded="false"
                    aria-haspopup="menu"
                >
                    <span class="relative grid size-8 shrink-0 place-items-center overflow-hidden rounded-full {{ $user->avatarColorClasses() }}">
                        <span class="text-sm font-semibold" aria-hidden="true">{{ $user->avatarInitial() }}</span>
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
                    <span class="hidden max-w-[104px] truncate text-sm font-semibold text-slate-700 lg:block">
                        {{ $user->firstName() }}
                    </span>
                    <i
                        data-lucide="chevron-down"
                        class="size-4 shrink-0 text-slate-400 transition-transform group-aria-expanded:rotate-180"
                        aria-hidden="true"
                    ></i>
                </button>

                <div
                    id="admin-profile-menu"
                    class="site-navbar__profile-menu absolute right-0 top-[calc(100%+12px)] z-20 hidden w-72 max-w-[calc(100vw-48px)] overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.18)]"
                    data-user-menu-panel
                    role="menu"
                    aria-hidden="true"
                >
                    <div class="border-b border-slate-200 px-5 py-4">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $user->displayName() }}</p>
                        <p class="mt-1 truncate text-xs text-slate-500">{{ $user->email }}</p>
                        <span class="mt-2 inline-flex rounded-full bg-sky-50 px-2 py-1 text-[11px] font-medium text-[#1684e5]">
                            ผู้ดูแลระบบ
                        </span>
                    </div>

                    <div class="p-2">
                        <a
                            href="{{ route('home') }}"
                            class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-[#1684e5] focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-200"
                            role="menuitem"
                        >
                            <i data-lucide="house" class="size-4" aria-hidden="true"></i>
                            <span>เข้าสู่หน้าหลัก</span>
                        </a>
                        <form action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-red-500 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-200"
                                role="menuitem"
                            >
                                <i data-lucide="log-out" class="size-4" aria-hidden="true"></i>
                                <span>ออกจากระบบ</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </nav>
</header>
