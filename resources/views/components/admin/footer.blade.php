
<footer class="mt-auto border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-1 sm:px-6 lg:flex-row lg:items-center lg:justify-center lg:gap-48 lg:px-8">

      
        <div class="flex items-center gap-3">
            <div>
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
                <p class="text-xs text-slate-500 px-4">
                    Course & Professor Rating
                </p>
            </div>
        </div>

        {{-- Description --}}
        <p class="text-xs leading-relaxed text-slate-500 lg:text-center">
            ระบบจัดการข้อมูลรายวิชา อาจารย์ และหัวข้อรีวิว
        </p>

        {{-- Copyright --}}
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                Admin Panel
            </span>

            <span class="text-xs text-slate-400">
                &copy; {{ date('Y') }} CPRATING
            </span>
        </div>

    </div>
</footer>
