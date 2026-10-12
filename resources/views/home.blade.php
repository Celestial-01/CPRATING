@extends('layouts.app')

@section('title', 'รีวิวรายวิชาก่อนลงทะเบียน')

@section('content')
    <x-navbar active="home" :home-hero="true" />

    @if ($showSkeleton)
        <x-skeleton.home />
    @else
        <main class="home-page source-page">
            <header class="hero" id="top" data-home-hero aria-labelledby="home-title">
                <div class="fx" aria-hidden="true">
                    <i class="rg rg--small"></i>
                    <i class="rg rg--large b"></i>
                    <i data-lucide="archive" class="dc dc--archive ic" aria-hidden="true"></i>
                    <i data-lucide="book-open" class="dc dc--book ic" aria-hidden="true"></i>
                    <i data-lucide="monitor" class="dc dc--monitor ic" aria-hidden="true"></i>
                    <i data-lucide="graduation-cap" class="dc dc--graduation ic" aria-hidden="true"></i>
                </div>

                <div id="flo" aria-hidden="true">
                    @foreach ($heroCards as $heroCard)
                        <x-home.hero-float-card
                            :course="$heroCard['course']"
                            :css-class="$heroCard['cssClass']"
                        />
                    @endforeach
                </div>

                <div class="wrap">
                    <h1 id="home-title">
                        <span class="hero-title-line">ทุกวิชามีเรื่องราว</span><br>
                        <span class="hero-title-line k">ก่อนคุณลงเรียน</span>
                    </h1>
                    <p>รวมคะแนนและรีวิวรายวิชาจากผู้เรียนจริง เพื่อให้คุณเลือกวิชาได้ชัดเจนขึ้น</p>
                </div>
            </header>

            <section>
                <div class="wrap">
                    <p id="st" data-home-statement aria-label="{{ $homeCopy['statement'] ?? '' }}"></p>
                </div>
            </section>

            <section id="courses" class="home-courses-section">
                <div class="wrap">
                    <div class="head">
                        <h2>วิชายอดนิยม<br><span class="k">ที่นักศึกษาสนใจ</span></h2>
                        <a class="btn g" href="{{ route('courses.index') }}">ค้นหาวิชาทั้งหมด <i data-lucide="arrow-up-right" class="ic size-4" aria-hidden="true"></i></a>
                    </div>
                </div>

                <div class="rail" id="rail" data-home-course-rail>
                    @foreach ($popularCourses as $course)
                        <x-course.card :course="$course" />
                    @endforeach
                </div>

                <div class="wrap ctl">
                    <div class="arrows">
                        <button type="button" data-home-rail-prev aria-label="รายวิชาก่อนหน้า"><i data-lucide="arrow-left" class="ic size-5" aria-hidden="true"></i></button>
                        <button type="button" data-home-rail-next aria-label="รายวิชาถัดไป"><i data-lucide="arrow-right" class="ic size-5" aria-hidden="true"></i></button>
                    </div>
                </div>
            </section>

            <section id="features" class="band">
                <div class="wrap"><h2>ไฮไลต์<br><span class="k">ของ CPRATING</span></h2></div>
                <div class="hl" id="hl" data-home-highlight-rail role="region" aria-label="ไฮไลต์ของ CPRATING" tabindex="0">
                    <article class="hc rating-highlight-card" data-home-highlight>
                        <p class="tx">
                            คะแนน <span class="k">rating คิดจากความน่าสนใจของรายวิชา</span>
                            ส่วนปริมาณงานและความยากแสดงเป็นระดับ น้อย–มาก ไม่ปนกับดาว
                        </p>

                        <div class="panel rating-highlight-panel">
                            <div class="rating-highlight-score">
                                <div class="rating-highlight-score-copy">
                                    <span class="rating-highlight-score-label">ความน่าสนใจของ<br>รายวิชา</span>
                                    <b data-count="4.6" data-count-start="0.0">0.0</b>
                                    <span class="rating-highlight-stars" aria-label="5 ดาว">★★★★★</span>
                                    <small>จาก 1,400 รีวิว</small>
                                </div>
                            </div>

                            <div class="rating-highlight-metrics">
                                <div class="rating-highlight-metric">
                                    <div class="rating-highlight-metric-heading">
                                        <span>ปริมาณงาน</span>
                                        <strong>ค่อนข้างมาก</strong>
                                    </div>
                                    <div
                                        class="rating-highlight-progress"
                                        role="progressbar"
                                        aria-label="ระดับปริมาณงาน"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-valuenow="0"
                                    >
                                        <i
                                            class="rating-highlight-progress-fill"
                                            data-highlight-progress="80"
                                            style="--progress-target: 80%;"
                                        ></i>
                                    </div>
                                    <div class="rating-highlight-progress-labels">
                                        <span>น้อย</span>
                                        <span>มาก</span>
                                    </div>
                                </div>

                                <div class="rating-highlight-metric">
                                    <div class="rating-highlight-metric-heading">
                                        <span>ความยาก</span>
                                        <strong>ปานกลาง</strong>
                                    </div>
                                    <div
                                        class="rating-highlight-progress"
                                        role="progressbar"
                                        aria-label="ระดับความยาก"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-valuenow="0"
                                    >
                                        <i
                                            class="rating-highlight-progress-fill"
                                            data-highlight-progress="60"
                                            style="--progress-target: 60%;"
                                        ></i>
                                    </div>
                                    <div class="rating-highlight-progress-labels">
                                        <span>น้อย</span>
                                        <span>มาก</span>
                                    </div>
                                </div>

                                <small class="rating-highlight-note">
                                    ระดับจากผู้เรียนที่กดรีวิว ไม่ปนรวมเป็นดาว
                                </small>
                            </div>
                        </div>
                    </article>

                    <article class="hc dk" data-home-highlight>
                        <img src="{{ asset('images/photo-6.jpg') }}" alt="">
                        <p class="tx">รีวิวจากผู้เรียนจริง <span class="hi">ยืนยันตัวตนด้วยอีเมลมหาวิทยาลัย</span> แต่ไม่เปิดเผยชื่อ จึงเขียนได้ตรงไปตรงมา</p>
                        <div class="glass"><div class="who"><i>ส</i>ผู้เรียนปี 3 · รีวิววิชาความมั่นคงปลอดภัยไซเบอร์</div><p>แล็บสนุก ได้ลงมือทำจริง แต่ต้องวางเวลาให้ดี ช่วงกลางเทอมงานจะเยอะ</p><div class="tg"><span>แล็บสนุก</span><span>งานกลางเทอมเยอะ</span></div></div>
                    </article>

                    <article class="hc" data-home-highlight>
                        <p class="tx">ภาระงานตามหน่วยกิต <span class="k">2-2-5</span> เห็นชั่วโมงบรรยาย ปฏิบัติ และศึกษาด้วยตนเองต่อสัปดาห์ ก่อนจัดตารางของคุณ</p>
                        <div class="panel"><div class="hrs"><b>9</b><span>ชั่วโมงต่อสัปดาห์</span></div><div class="seg"><i></i><i></i><i></i></div><div class="segl"><span>บรรยาย 2</span><span>ปฏิบัติ 2</span><span>ศึกษาเอง 5</span></div></div>
                    </article>

                    <article class="hc curriculum-highlight-card" data-home-highlight>
                        <p class="tx curriculum-highlight-title">
                            <span>วิชาที่ต้องเรียน</span>ของแต่ละสาขา<br>
                            ตั้งแต่ปี 1 ถึงปี 4
                        </p>

                        <div class="curriculum-highlight-panel">
                            <div class="curriculum-highlight-heading">
                                <div>
                                    <small>สาขา</small>
                                    <strong>{{ $curriculumPreview['major_name'] }}</strong>
                                </div>
                                <a href="{{ route('plans.index') }}">
                                    ดูทุกสาขา
                                    <i data-lucide="arrow-right" class="ic size-4" aria-hidden="true"></i>
                                </a>
                            </div>

                            <div class="curriculum-highlight-years">
                                @foreach ($curriculumPreview['years'] as $year)
                                    <div class="curriculum-highlight-year">
                                        <span class="curriculum-highlight-year-label">{{ $year['label'] }}</span>
                                        <div class="curriculum-highlight-course-list">
                                            @foreach ($year['courses'] as $course)
                                                <span>{{ $course }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>
                </div>
                <div class="wrap ctl2"><button class="cb" type="button" data-home-highlight-toggle aria-label="หยุดเล่นอัตโนมัติ"><i data-lucide="pause" class="ic size-4" aria-hidden="true"></i></button><button class="cb" type="button" data-home-highlight-prev aria-label="ไฮไลต์ก่อนหน้า"><i data-lucide="arrow-left" class="ic size-4" aria-hidden="true"></i></button><button class="cb" type="button" data-home-highlight-next aria-label="ไฮไลต์ถัดไป"><i data-lucide="arrow-right" class="ic size-4" aria-hidden="true"></i></button></div>
            </section>

            <section id="steps"><div class="wrap"><h2 class="how-h">ใช้งานง่าย<br><span class="k">ใน 3 ขั้นตอน</span></h2><div class="how">
                <figure><div class="tile"><div class="ui"><div class="sf"><i data-lucide="search" class="ic size-4" aria-hidden="true"></i><span>CP422301</span><i class="caret"></i></div><div class="rs"><span><b>CP422301</b> ความมั่นคงปลอดภัยไซเบอร์</span><b>★ 4.6</b></div><div class="rs"><span><b>CP422305</b> การเข้ารหัสลับเบื้องต้น</span><b>★ 4.3</b></div></div></div><figcaption><p><small>ขั้นที่ 1</small><b>ค้นหาวิชาได้ทันที</b> พิมพ์รหัสหรือชื่อวิชา แล้วกรองตามหมวดที่สนใจ ไม่ต้องไล่ดูทีละหน้า</p></figcaption></figure>
                        <figure>
                            <div class="tile">
                                <div class="ui">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex min-w-0 items-center gap-2.5">
                                            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-[#f8c4a5] text-sm font-semibold text-[#6b3f32]" aria-hidden="true">ม</span>
                                            <span class="truncate text-sm font-semibold text-slate-900">มินท์</span>
                                        </div>
                                        <span class="shrink-0 text-sm tracking-normal text-orange-400" aria-label="4 จาก 5 ดาว">★★★★<span class="text-slate-300">★</span></span>
                                    </div>

                                    <div class="mt-3 text-sm leading-6 text-slate-900">
                                        อาจารย์อธิบายชัด สไลด์<br>
                                        ครบ ข้อสอบตรงกับที่สอน
                                    </div>

                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        <x-home.review-level label="ภาระงาน" :score="3" level="ปานกลาง" />
                                        <x-home.review-level label="ความยาก" :score="2" level="ค่อนข้างน้อย" />
                                    </div>
                                </div>
                            </div>
                            <figcaption><p><small>ขั้นที่ 2</small><b>อ่านคะแนนและรีวิวจริง</b> เปรียบเทียบเนื้อหา ภาระงาน และความยากจากคนที่เคยเรียนมาแล้ว</p></figcaption>
                        </figure>
                <figure><div class="tile"><div class="ui"><div class="pmeta step-plan-meta"><b>เทอม 1 ปี 3</b><b>9 / 18 หน่วยกิต</b></div><div class="prow"><span>CP422301 ความมั่นคงปลอดภัยไซเบอร์</span><b>3</b></div><div class="prow"><span>CP354101 ปัญญาประดิษฐ์</span><b>3</b></div><div class="prow"><span>CP352005 เครือข่ายคอมพิวเตอร์</span><b>3</b></div></div></div><figcaption><p><small>ขั้นที่ 3</small><b>เลือกสาขา แล้วจัดแผนการเรียน</b> เลือกหลักสูตรของสาขาที่เรียน ดูหน่วยกิตรวม แล้วลงทะเบียนอย่างมั่นใจ</p></figcaption></figure>
            </div></div></section>

            <section id="reviews"><div class="wrap"><div class="rvh"><h2>เสียงจาก<br><span class="k">คนที่เคยเรียน</span></h2><p>อ่านรีวิวล่าสุดจากผู้เรียนจริง ทั้งข้อดี ข้อควรระวัง และเคล็ดลับก่อนลงทะเบียน</p></div><div class="rvg">
                @foreach ($reviews as $review)
                    <x-home.review-card :review="$review" />
                @endforeach
            </div><div class="rvm"><a class="btn g" href="{{ route('courses.index') }}">ดูรีวิวทั้งหมด <i data-lucide="arrow-up-right" class="ic size-4" aria-hidden="true"></i></a></div></div></section>

            <section id="trust"><div class="wrap tw"><div><p class="eb">หลักการของเรา</p><h2>รีวิวที่<span class="k">เชื่อถือได้</span><br>มาจากผู้เรียนจริง</h2><p class="ld">CPRATING รวมคะแนนและรีวิวรายวิชา ให้คุณเปรียบเทียบเนื้อหา ภาระงาน และความยาก ก่อนตัดสินใจลงทะเบียน</p><a class="lk" href="{{ route('courses.index') }}">เริ่มสำรวจรายวิชา <i data-lucide="arrow-up-right" class="ic size-4" aria-hidden="true"></i></a></div><ul class="tl"><li><i class="iw"><i data-lucide="graduation-cap" class="ic size-6" aria-hidden="true"></i></i><div><b>ยืนยันด้วยอีเมลมหาวิทยาลัย</b><span>ทุกรีวิวมาจากบัญชีที่ยืนยันตัวตนแล้ว</span></div></li><li><i class="iw"><i data-lucide="star" class="ic size-6" aria-hidden="true"></i></i><div><b>คะแนนแยกตามหัวข้อ</b><span>ไม่สรุปเป็นดาวรวมค่าเดียว</span></div></li><li><i class="iw"><i data-lucide="eye-off" class="ic size-6" aria-hidden="true"></i></i><div><b>ไม่เปิดเผยชื่อผู้รีวิว</b><span>เขียนได้ตรงไปตรงมา ไม่ต้องกังวล</span></div></li></ul></div></section>

            <section id="faq" class="home-faq-section"><div class="wrap ctr"><h2>คำถามที่<span class="k">พบบ่อย</span></h2><div class="faq faq-align-left"><details><summary>ใครเขียนรีวิวได้บ้าง</summary><div class="a"><p>นักศึกษาที่ยืนยันตัวตนด้วยอีเมลมหาวิทยาลัยเท่านั้น เพื่อให้มั่นใจว่ารีวิวมาจากผู้เรียนจริง</p></div></details><details><summary>ชื่อของผู้รีวิวจะถูกเปิดเผยไหม</summary><div class="a"><p>ไม่เปิดเผย รีวิวจะแสดงเพียงชั้นปี เพื่อให้เขียนได้ตรงไปตรงมาโดยไม่ต้องกังวล</p></div></details><details><summary>ทำไมไม่ให้ดาวรวมค่าเดียว</summary><div class="a"><p>เพราะวิชาที่เนื้อหาดีอาจมีงานหนัก หรือวิชาที่ง่ายอาจไม่ได้ความรู้มากนัก เราจึงแยกคะแนนเป็นเนื้อหา ภาระงาน ความยาก และผู้สอน ให้คุณเทียบได้ตรงกับสิ่งที่สนใจ</p></div></details><details><summary>ตัวเลขหน่วยกิต 2-2-5 หมายถึงอะไร</summary><div class="a"><p>คือชั่วโมงบรรยาย ปฏิบัติ และศึกษาด้วยตนเองต่อสัปดาห์ ตามลำดับ ช่วยให้ประเมินภาระงานรวมก่อนจัดตารางได้</p></div></details><details><summary>ใช้งานฟรีหรือไม่</summary><div class="a"><p>ค้นหาวิชาและอ่านรีวิวได้ฟรี ส่วนการบันทึกวิชา จัดแผนการเรียน และเขียนรีวิว ต้องเข้าสู่ระบบด้วยอีเมลมหาวิทยาลัย</p></div></details></div></div></section>

        </main>
    @endif

    <script type="application/json" data-home-copy>@json($homeCopy)</script>
    <div id="prog" aria-hidden="true"><i></i></div>
    <div id="toast" role="status" aria-live="polite"></div>
    <button id="top-btn" type="button" aria-label="กลับขึ้นบน"><i data-lucide="arrow-up" class="ic size-5" aria-hidden="true"></i></button>

    <x-footer :flush="true" />
@endsection
