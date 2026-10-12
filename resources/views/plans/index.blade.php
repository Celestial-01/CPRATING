@extends('layouts.app')

@section('title', 'แผนการเรียน')

@section('content')
    <x-navbar active="plans" />

    <main class="pg source-page" data-plans-page>
        <div class="wrap">
            <section id="v1" data-plan-selection aria-labelledby="plans-title">
                <p class="eb">แผนการเรียน</p>
                <h1 id="plans-title">เลือก<span class="k">หลักสูตร</span>ของคุณ</h1>
                <p class="ld">เลือกสาขาที่คุณเรียนก่อน แล้วดูแผนการเรียนตลอดหลักสูตร</p>
                <div class="mg" id="mg" data-major-grid>
                    @foreach ($majors as $major)
                        <button type="button" class="mc2" data-major-key="{{ $major['key'] }}">
                            <span class="im"><img src="{{ $major['image'] }}" alt=""></span>
                            <b>{{ $major['name'] }}</b>
                            <span class="s">ดูแผนการเรียน</span>
                        </button>
                    @endforeach
                </div>
            </section>

            <section id="v2" data-plan-detail hidden aria-labelledby="mt">
                <button type="button" class="back" id="back" data-plan-back><i data-lucide="arrow-left" class="ic size-4" aria-hidden="true"></i>เปลี่ยนสาขา</button>
                <p class="eb">แผนการเรียน</p>
                <h1 id="mt" data-plan-major-title></h1>
                <p class="ld">สำหรับนักศึกษาที่เข้าเรียนปีการศึกษา {{ $academicYearStart }} เป็นต้นไป</p>
                <div class="yr" id="yr" data-plan-year-tabs role="group" aria-label="เลือกชั้นปี"></div>
                <div id="pts" data-plan-term-list></div>
                <p class="nt">รายวิชาเป็นข้อมูลตัวอย่าง ในเว็บจริงจะดึงตามหลักสูตรของแต่ละสาขา</p>
            </section>
        </div>
    </main>

    <script type="application/json" data-study-plans>@json($plans)</script>
    <script type="application/json" data-plan-years>@json($years)</script>
    <script type="application/json" data-plan-copy>@json($planCopy)</script>
    <template data-plan-year-template>
        <button type="button" data-plan-year></button>
    </template>
    <template data-plan-term-template>
        <article class="pt">
            <h3 data-plan-term-heading></h3>
            <div class="tw2">
                <table>
                    <thead>
                        <tr>
                            <th>รหัสวิชา</th>
                            <th>ชื่อวิชา</th>
                            <th>หน่วยกิต</th>
                            <th>เงื่อนไขของรายวิชา</th>
                        </tr>
                    </thead>
                    <tbody data-plan-term-body>
                        <tr class="sum f" data-plan-registered-row>
                            <td colspan="2">รวมจำนวนหน่วยกิตลงทะเบียนเรียน</td>
                            <td class="cr" data-plan-registered-credits></td>
                            <td>หน่วยกิต</td>
                        </tr>
                        <tr class="sum" data-plan-accumulated-row>
                            <td colspan="2">รวมจำนวนหน่วยกิตสะสม</td>
                            <td class="cr" data-plan-accumulated-credits></td>
                            <td>หน่วยกิต</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </template>
    <template data-plan-course-row-template>
        <tr>
            <td class="cd" data-plan-course-code></td>
            <td>
                <span data-plan-course-name></span>
                <small data-plan-course-english></small>
            </td>
            <td class="cr" data-plan-course-credits></td>
            <td data-plan-course-prerequisite></td>
        </tr>
    </template>
    <div id="toast" role="status" aria-live="polite"></div>
    <x-footer />
@endsection
