<section
    data-plan-detail
    data-major-key="{{ $majorCode }}"
    aria-labelledby="plan-major-title-{{ $majorCode }}"
    hidden
>
    <button type="button" class="back" data-plan-back>
        <i data-lucide="arrow-left" class="ic size-4" aria-hidden="true"></i>
        เปลี่ยนสาขา
    </button>

    <p class="eb">แผนการเรียน</p>
    <h1 id="plan-major-title-{{ $majorCode }}">{{ $studyPlan['name'] }}</h1>
    <p class="ld" data-academic-year="{{ $academicYearStart }}">
        สำหรับนักศึกษาที่เข้าเรียนปีการศึกษา {{ $academicYearStart }} เป็นต้นไป
    </p>

    <div class="yr" data-plan-year-tabs role="group" aria-label="เลือกชั้นปี">
        @foreach ($years as $year)
            <button type="button" data-plan-year="{{ $year }}" aria-pressed="false">
                ชั้นปีที่ {{ $year }}
            </button>
        @endforeach
    </div>

    <div data-plan-term-list>
        @foreach ($years as $year)
            <div data-plan-year-panel="{{ $year }}" hidden>
                @foreach ($studyPlan['years'][$year] as $term => $termPlan)
                    @include('plans.term')
                @endforeach
            </div>
        @endforeach
    </div>
</section>
