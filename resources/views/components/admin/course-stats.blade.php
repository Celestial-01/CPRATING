{{-- ค่าเฉลี่ย (รอข้อมูลจากฝั่ง user) --}}
<section class="mt-6 grid gap-5 md:grid-cols-2" aria-label="ค่าเฉลี่ยรายวิชา">
    @foreach ($metrics as $metric)
        <article class="rounded-[21px] border border-slate-200 bg-white p-5 sm:p-6">
            <h2 class="text-base font-semibold text-slate-900">{{ $metric['title'] }}</h2>

            <div class="mt-7 flex items-center justify-between text-sm">
                <span class="text-slate-500">{{ $metric['label'] }}</span>
                <span class="font-semibold text-slate-400">- / 5</span>
            </div>

            <div class="mt-2 h-2 rounded-full bg-slate-100" role="progressbar" aria-valuemin="0" aria-valuemax="5"></div>

            <p class="mt-3 text-xs text-slate-400">ยังไม่มีข้อมูลจากผู้ใช้</p>
        </article>
    @endforeach
</section>

{{-- โอกาสได้เกรด (รอข้อมูลจากฝั่ง user) --}}
<section class="mt-6 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8" aria-label="โอกาสได้เกรด">
    <div class="grid gap-8 sm:grid-cols-[230px_1fr] sm:items-end sm:gap-10">
        <div>
            <h2 class="text-base font-semibold text-slate-900">เกรดเฉลี่ยของวิชานี้</h2>
            <p class="mt-3 text-5xl font-bold leading-none text-slate-300">-</p>
            <p class="mt-4 text-sm text-slate-500">คำนวณจากเกรดที่ผู้เรียนรายงาน</p>
            <p class="mt-5 text-xs text-slate-400">ยังไม่มีข้อมูลจากผู้ใช้</p>
        </div>

        <div class="flex h-32 items-end justify-between gap-2 sm:gap-4">
            @foreach ($grades as $grade)
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-[11px] text-slate-400">-%</span>
                    <div class="h-2 w-full max-w-16 rounded-t-[17px] bg-slate-100"></div>
                    <span class="text-xs font-medium text-slate-500">{{ $grade }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
