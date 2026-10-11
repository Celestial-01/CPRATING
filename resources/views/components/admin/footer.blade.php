
<footer class="mt-auto border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">

      
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-sm">
                C
            </div>

            <div>
                <p class="text-sm font-bold tracking-wide text-slate-800">
                    CPRATING
                </p>
                <p class="text-xs text-slate-500">
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
