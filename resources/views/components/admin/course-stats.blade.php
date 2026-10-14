<section class="mt-6 grid gap-5 md:grid-cols-2" aria-label="ค่าเฉลี่ยรายวิชา">
    @foreach ($metrics as $metric)
        <article class="rounded-[21px] border border-slate-200 bg-white p-5 sm:p-6">
            <h2 class="text-base font-semibold text-slate-900">{{ $metric['title'] }}</h2>

            <div class="mt-7 flex items-center justify-between text-sm">
                <span class="text-slate-500">คะแนนเฉลี่ย</span>
                <span class="font-semibold text-slate-700">
                    {{ $metric['average'] === null ? '-' : number_format($metric['average'], 1) }} / 5
                </span>
            </div>

            <div
                class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100"
                role="progressbar"
                aria-label="{{ $metric['title'] }}"
                aria-valuemin="0"
                aria-valuemax="5"
                @if ($metric['average'] !== null) aria-valuenow="{{ $metric['average'] }}" @endif
            >
                <div class="h-full rounded-full bg-[#1684e5]" style="width: {{ $metric['percentage'] ?? 0 }}%"></div>
            </div>

            <p class="mt-3 text-xs text-slate-400">
                @if ($course->ratings_count > 0)
                    {{ $metric['percentage'] }}% · จากผู้ให้คะแนน {{ number_format($course->ratings_count) }} คน
                @else
                    ยังไม่มีข้อมูลจากผู้ใช้
                @endif
            </p>
        </article>
    @endforeach
</section>

<section class="mt-6 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8" aria-label="เกรดเฉลี่ยรายวิชา">
    <div class="grid gap-8 sm:grid-cols-[230px_1fr] sm:items-end sm:gap-10">
        <div>
            <h2 class="text-base font-semibold text-slate-900">เกรดเฉลี่ยของวิชานี้</h2>
            <p class="mt-3 text-5xl font-bold leading-none text-slate-700">
                {{ $averageGradePoint === null ? '-' : number_format($averageGradePoint, 2) }}
            </p>
            <p class="mt-4 text-sm text-slate-500">คำนวณจากเกรดที่ผู้เรียนรายงาน</p>
            <p class="mt-5 text-xs text-slate-400">
                {{ $gradeSampleSize > 0 ? 'จากผู้รายงานเกรด '.number_format($gradeSampleSize).' คน' : 'ยังไม่มีข้อมูลจากผู้ใช้' }}
            </p>
        </div>

        <div class="flex h-32 items-end justify-between gap-2 sm:gap-4">
            @foreach ($gradeCounts as $grade => $count)
                @php $percentage = $course->gradePercentage($grade, $gradeCounts); @endphp
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-[11px] text-slate-400">{{ $percentage === null ? '-' : $percentage }}%</span>
                    <div
                        class="min-h-2 w-full max-w-16 rounded-t-[17px] bg-[#1684e5]"
                        style="height: {{ $percentage ?? 0 }}%"
                        aria-label="เกรด {{ $grade }}: {{ number_format($count) }} คน"
                    ></div>
                    <span class="text-xs font-medium text-slate-500">{{ $grade }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
