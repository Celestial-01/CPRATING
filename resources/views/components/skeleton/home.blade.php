<main class="bg-[#f8fafc]" aria-busy="true" aria-label="กำลังโหลดหน้าแรก">
    <section class="home-hero relative flex min-h-[100svh] items-center justify-center overflow-hidden bg-[#f4f5f7] px-5 py-24" aria-hidden="true">
        <div class="absolute inset-x-0 top-[13svh] flex justify-center gap-3 overflow-hidden py-4 sm:gap-6">
            @foreach (range(1, 12) as $index)
                <div class="size-16 shrink-0 animate-pulse rounded-[16px] bg-slate-200 motion-reduce:animate-none sm:size-24"></div>
            @endforeach
        </div>
        <div class="relative z-10 flex w-full max-w-[690px] flex-col items-center gap-5">
            <div class="h-14 w-4/5 animate-pulse rounded-xl bg-slate-200 motion-reduce:animate-none sm:h-20"></div>
            <div class="h-14 w-3/5 animate-pulse rounded-xl bg-slate-200 motion-reduce:animate-none sm:h-20"></div>
            <div class="mt-3 h-4 w-3/4 animate-pulse rounded bg-slate-100 motion-reduce:animate-none"></div>
            <div class="h-4 w-1/2 animate-pulse rounded bg-slate-100 motion-reduce:animate-none"></div>
        </div>
        <div class="absolute inset-x-0 bottom-[13svh] flex justify-center gap-3 overflow-hidden py-4 sm:gap-6">
            @foreach (range(1, 12) as $index)
                <div class="size-16 shrink-0 animate-pulse rounded-[16px] bg-slate-200 motion-reduce:animate-none sm:size-24"></div>
            @endforeach
        </div>
    </section>

    <section class="home-section" aria-hidden="true">
        <div class="home-container">
            <div class="mb-10 h-10 w-64 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
            <div class="home-course-grid">
                @foreach (range(1, 5) as $index)
                    <div>
                        <div class="aspect-[0.82] animate-pulse rounded-[20px] bg-slate-200 motion-reduce:animate-none"></div>
                        <div class="mt-4 h-5 w-4/5 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
                        <div class="mt-3 h-4 w-1/2 animate-pulse rounded bg-slate-100 motion-reduce:animate-none"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section bg-white" aria-hidden="true">
        <div class="home-container">
            <div class="mb-10 h-10 w-64 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
            <div class="grid gap-10 lg:grid-cols-[minmax(0,1.25fr)_minmax(0,0.75fr)]">
                <div>
                    <div class="aspect-[1.45] animate-pulse rounded-[20px] bg-slate-200 motion-reduce:animate-none"></div>
                    <div class="mt-5 h-6 w-3/4 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
                    <div class="mt-3 h-4 w-full animate-pulse rounded bg-slate-100 motion-reduce:animate-none"></div>
                </div>
                <div class="space-y-5">
                    @foreach (range(1, 3) as $index)
                        <div class="flex gap-4">
                            <div class="size-28 shrink-0 animate-pulse rounded-[20px] bg-slate-200 motion-reduce:animate-none"></div>
                            <div class="flex-1 space-y-3 py-2">
                                <div class="h-4 w-1/2 animate-pulse rounded bg-sky-100 motion-reduce:animate-none"></div>
                                <div class="h-5 w-full animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
                                <div class="h-4 w-2/3 animate-pulse rounded bg-slate-100 motion-reduce:animate-none"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</main>
