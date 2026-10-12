<div
    {{ $attributes }}
    aria-busy="true"
    aria-label="กำลังโหลดรายละเอียดรายวิชา"
>
    <section
        class="rounded-[24px] border border-slate-200 bg-white p-3 md:p-7"
        aria-hidden="true"
    >
        <div class="grid items-stretch gap-7 min-[640px]:grid-cols-[minmax(0,0.44fr)_minmax(0,1fr)]">
            <div class="min-w-0">
                <div class="aspect-[300/313] w-full rounded-[16px] {{ $pulse }}"></div>
                <div class="mt-6 grid grid-cols-3 gap-2.5">
                    @for ($index = 0; $index < 3; $index++)
                        <div class="rounded-[14px] border border-slate-100 bg-slate-50 px-2 py-3 text-center">
                            <div class="mx-auto h-5 w-12 rounded {{ $pulse }}"></div>
                            <div class="mx-auto mt-2 h-3 w-14 rounded {{ $softPulse }}"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="flex min-w-0 flex-col space-y-4">
                <div class="flex flex-wrap gap-1.5">
                    <div class="h-6 w-20 rounded-full {{ $softPulse }}"></div>
                    <div class="h-6 w-24 rounded-full {{ $softPulse }}"></div>
                    <div class="h-6 w-28 rounded-full {{ $softPulse }}"></div>
                </div>
                <div class="h-8 w-4/5 rounded {{ $pulse }}"></div>
                <div class="h-5 w-2/5 rounded {{ $softPulse }}"></div>
                <div class="h-4 w-3/5 rounded {{ $softPulse }}"></div>

                <div class="rounded-[16px] border-2 border-slate-100 bg-slate-50 px-4 py-4">
                    <div class="space-y-2">
                        <div class="h-4 w-full rounded {{ $softPulse }}"></div>
                        <div class="h-4 w-11/12 rounded {{ $softPulse }}"></div>
                        <div class="h-4 w-3/5 rounded {{ $softPulse }}"></div>
                    </div>
                    <div class="mt-3 h-3 w-40 rounded {{ $softPulse }}"></div>
                </div>

                <div class="mt-auto grid grid-cols-1 gap-3.5 pt-1 min-[640px]:grid-cols-[minmax(0,1fr)_minmax(0,0.4fr)_minmax(0,0.4fr)]">
                    <div class="h-12 rounded-[18px] {{ $pulse }}"></div>
                    <div class="h-12 rounded-[18px] border border-slate-200 {{ $softPulse }}"></div>
                    <div class="h-12 rounded-[18px] border border-slate-200 {{ $softPulse }}"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-5 md:grid-cols-2" aria-hidden="true">
        @for ($index = 0; $index < 2; $index++)
            <article class="rounded-[21px] border border-slate-200 bg-white p-5 sm:p-6">
                <div class="h-5 w-44 rounded {{ $pulse }}"></div>
                <div class="mt-7 flex items-center justify-between">
                    <div class="h-4 w-20 rounded {{ $softPulse }}"></div>
                    <div class="h-4 w-10 rounded {{ $pulse }}"></div>
                </div>
                <div class="mt-2 h-2 rounded-full {{ $softPulse }}"></div>
                <div class="mt-3 h-8 w-full rounded {{ $softPulse }}"></div>
            </article>
        @endfor
    </section>

    <section class="mt-6 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8" aria-hidden="true">
        <div class="grid gap-8 sm:grid-cols-[230px_1fr] sm:items-end sm:gap-10">
            <div>
                <div class="h-5 w-40 rounded {{ $pulse }}"></div>
                <div class="mt-3 h-14 w-36 rounded {{ $pulse }}"></div>
                <div class="mt-4 h-10 w-full rounded {{ $softPulse }}"></div>
                <div class="mt-5 h-8 w-full rounded {{ $softPulse }}"></div>
            </div>
            <div class="flex h-32 items-end justify-between gap-2 sm:gap-4">
                @for ($index = 0; $index < 8; $index++)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <div class="h-3 w-7 rounded {{ $softPulse }}"></div>
                        <div
                            class="course-detail-skeleton-bar w-full max-w-16 rounded-t-[17px] {{ $index === 7 ? $pulse : $softPulse }}"
                            data-bar-index="{{ $index }}"
                        ></div>
                        <div class="h-3 w-5 rounded {{ $softPulse }}"></div>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <section class="mt-8 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8" aria-hidden="true">
        <div class="h-6 w-48 rounded {{ $pulse }}"></div>
        <div class="mt-5 space-y-3">
            <div class="h-4 w-full rounded {{ $softPulse }}"></div>
            <div class="h-4 w-11/12 rounded {{ $softPulse }}"></div>
            <div class="h-4 w-4/5 rounded {{ $softPulse }}"></div>
            <div class="h-4 w-3/5 rounded {{ $softPulse }}"></div>
        </div>
        <div class="my-7 border-t border-slate-100"></div>
        <div class="h-5 w-52 rounded {{ $pulse }}"></div>
        <div class="mt-4 flex flex-wrap gap-2">
            @for ($index = 0; $index < 6; $index++)
                <div class="h-8 w-28 rounded-full {{ $softPulse }}"></div>
            @endfor
        </div>
    </section>

    <section class="mt-8 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8" aria-hidden="true">
        <div class="h-6 w-56 rounded {{ $pulse }}"></div>
        <div class="mt-6 grid gap-7 md:grid-cols-2">
            @for ($column = 0; $column < 2; $column++)
                <div>
                    <div class="h-5 w-44 rounded {{ $pulse }}"></div>
                    <div class="mt-4 space-y-3">
                        @for ($row = 0; $row < 2; $row++)
                            <div class="flex items-center justify-between rounded-[16px] border border-slate-100 bg-slate-50 px-3 py-3">
                                <div class="space-y-2">
                                    <div class="h-4 w-24 rounded {{ $pulse }}"></div>
                                    <div class="h-3 w-36 rounded {{ $softPulse }}"></div>
                                </div>
                                <div class="h-6 w-16 rounded-full {{ $softPulse }}"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <section class="mt-8 rounded-[22px] border border-slate-200 bg-white p-5 sm:p-6" aria-hidden="true">
        <div class="flex items-center justify-between gap-4">
            <div class="h-6 w-44 rounded {{ $pulse }}"></div>
            <div class="h-8 w-36 rounded-full {{ $softPulse }}"></div>
        </div>
        <div class="mt-6 flex gap-3">
            <div class="size-9 shrink-0 rounded-full {{ $softPulse }}"></div>
            <div class="h-20 flex-1 rounded-[14px] {{ $softPulse }}"></div>
        </div>
        <div class="mt-6 space-y-5">
            @for ($index = 0; $index < 3; $index++)
                <div class="flex gap-3">
                    <div class="size-9 shrink-0 rounded-full {{ $softPulse }}"></div>
                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="h-4 w-32 rounded {{ $pulse }}"></div>
                        <div class="h-3 w-full rounded {{ $softPulse }}"></div>
                        <div class="h-3 w-4/5 rounded {{ $softPulse }}"></div>
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <section class="mt-9" aria-hidden="true">
        <div class="h-7 w-56 rounded {{ $pulse }}"></div>
        <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @for ($index = 0; $index < 4; $index++)
                <x-skeleton.course-card />
            @endfor
        </div>
    </section>
</div>
