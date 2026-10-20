@extends('layouts.app')

@section('title', 'ประวัติการใช้งาน')

@section('content')
    <x-navbar active="history" />

    <main class="content-page source-page history-page" data-history-page>
        <div class="page-container">
            <header class="history-header">
                <p class="section-eyebrow">กิจกรรมของคุณ</p>
                <h1 id="history-title" tabindex="-1">ประวัติ<span class="gradient-text">การใช้งาน</span></h1>
                <p class="page-lead">กลับมาดูวิชาที่คุณเพิ่งเปิดไว้ หรือเข้าถึงวิชาที่บันทึกเก็บไว้ได้จากที่เดียว</p>
            </header>

            <section class="history-section" data-history-section="viewed" aria-labelledby="history-viewed-title">
                <div class="history-section__header">
                    <div>
                        <p class="history-section__eyebrow"><i data-lucide="clock" class="ui-icon size-4" aria-hidden="true"></i> ดูล่าสุด</p>
                        <h2 id="history-viewed-title" tabindex="-1">วิชาที่คุณเพิ่งเปิดดู <span data-history-viewed-count>0</span></h2>
                        <p data-history-viewed-summary>กำลังโหลดรายการ</p>
                    </div>
                    <button
                        type="button"
                        class="group inline-flex min-h-11 shrink-0 cursor-pointer items-center gap-2 text-sm font-medium text-slate-700 transition-colors duration-200 hover:text-[#1684e5] focus-visible:text-[#1684e5] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#1684e5]"
                        data-history-show="viewed"
                        hidden
                    >
                        <span class="underline-offset-4 group-hover:underline group-focus-visible:underline">ดูทั้งหมด</span>
                        <i data-lucide="arrow-right" class="ui-icon size-4" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="inline-flex shrink-0 cursor-pointer items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors duration-200 hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500" data-history-overview hidden>
                        <i data-lucide="arrow-left" class="ui-icon size-4" aria-hidden="true"></i>
                        กลับสู่ภาพรวม
                    </button>
                </div>

                <div class="history-grid" data-history-viewed-grid>
                    @foreach ($viewedCourses as $course)
                        <x-course.card :course="$course" :history="true" />
                    @endforeach
                </div>
                <div class="history-rail-controls" data-history-rail-controls hidden>
                    <button
                        type="button"
                        class="history-rail-control"
                        data-history-rail-prev="viewed"
                        aria-label="เลื่อนวิชาที่เปิดดูกลับ"
                    >
                        <i data-lucide="arrow-left" class="ui-icon size-5" aria-hidden="true"></i>
                    </button>
                    <button
                        type="button"
                        class="history-rail-control"
                        data-history-rail-next="viewed"
                        aria-label="เลื่อนวิชาที่เปิดดูถัดไป"
                    >
                        <i data-lucide="arrow-right" class="ui-icon size-5" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="history-empty" data-history-viewed-empty hidden>
                    <span class="history-empty__icon"><i data-lucide="clock" class="size-5" aria-hidden="true"></i></span>
                    <strong>ยังไม่มีประวัติการเปิดดู</strong>
                    <p>เมื่อคุณเปิดดูรายละเอียดวิชา รายการจะถูกเก็บไว้ที่นี่</p>
                    <a class="history-empty__link" href="{{ route('courses.index') }}">
                        ค้นหารายวิชา
                        <i data-lucide="arrow-up-right" class="ui-icon size-4" aria-hidden="true"></i>
                    </a>
                </div>
            </section>

            <section class="history-section history-section--saved" data-history-section="saved" aria-labelledby="history-saved-title">
                <div class="history-section__header">
                    <div>
                        <p class="history-section__eyebrow"><i data-lucide="bookmark" class="ui-icon size-4" aria-hidden="true"></i> บันทึกไว้</p>
                        <h2 id="history-saved-title" tabindex="-1">วิชาที่คุณเก็บไว้ <span data-history-saved-count>0</span></h2>
                        <p data-history-saved-summary>กำลังโหลดรายการ</p>
                    </div>
                    <button
                        type="button"
                        class="group inline-flex min-h-11 shrink-0 cursor-pointer items-center gap-2 text-sm font-medium text-slate-700 transition-colors duration-200 hover:text-[#1684e5] focus-visible:text-[#1684e5] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#1684e5]"
                        data-history-show="saved"
                        hidden
                    >
                        <span class="underline-offset-4 group-hover:underline group-focus-visible:underline">ดูทั้งหมด</span>
                        <i data-lucide="arrow-right" class="ui-icon size-4" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="inline-flex shrink-0 cursor-pointer items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors duration-200 hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500" data-history-overview hidden>
                        <i data-lucide="arrow-left" class="ui-icon size-4" aria-hidden="true"></i>
                        กลับสู่ภาพรวม
                    </button>
                </div>

                <div class="history-grid" data-history-saved-grid>
                    @foreach ($savedCourses as $course)
                        <x-course.card :course="$course" :history="true" />
                    @endforeach
                </div>
                <div class="history-rail-controls" data-history-rail-controls hidden>
                    <button
                        type="button"
                        class="history-rail-control"
                        data-history-rail-prev="saved"
                        aria-label="เลื่อนวิชาที่บันทึกกลับ"
                    >
                        <i data-lucide="arrow-left" class="ui-icon size-5" aria-hidden="true"></i>
                    </button>
                    <button
                        type="button"
                        class="history-rail-control"
                        data-history-rail-next="saved"
                        aria-label="เลื่อนวิชาที่บันทึกถัดไป"
                    >
                        <i data-lucide="arrow-right" class="ui-icon size-5" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="history-empty" data-history-saved-empty hidden>
                    <span class="history-empty__icon"><i data-lucide="bookmark" class="size-5" aria-hidden="true"></i></span>
                    <strong>ยังไม่มีวิชาที่บันทึกไว้</strong>
                    <p>กดไอคอนบุ๊กมาร์กบนการ์ดวิชา เพื่อเก็บวิชาที่อยากกลับมาดู</p>
                    <a class="history-empty__link" href="{{ route('courses.index') }}">
                        เลือกวิชาที่สนใจ
                        <i data-lucide="arrow-up-right" class="ui-icon size-4" aria-hidden="true"></i>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <div id="toast" role="status" aria-live="polite"></div>
    <script type="application/json" data-history-copy>@json($historyCopy)</script>
    <x-footer />
@endsection
