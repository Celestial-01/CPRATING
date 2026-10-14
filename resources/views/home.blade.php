@extends('layouts.app')

@section('title', 'รีวิวรายวิชาก่อนลงทะเบียน')

@section('content')
    <x-navbar active="home" :home-hero="true" />

    @if ($showSkeleton)
        <x-skeleton.home />
    @else
        <main class="home-page source-page">
            <header class="home-hero" id="top" data-home-hero aria-labelledby="home-title">
                <div class="hero-effects" aria-hidden="true">
                    <i class="hero-orbit hero-orbit--small"></i>
                    <i class="hero-orbit hero-orbit--large hero-orbit--reverse"></i>
                    <i data-lucide="archive" class="hero-decor-icon hero-decor-icon--archive ui-icon" aria-hidden="true"></i>
                    <i data-lucide="book-open" class="hero-decor-icon hero-decor-icon--book ui-icon" aria-hidden="true"></i>
                    <i data-lucide="monitor" class="hero-decor-icon hero-decor-icon--monitor ui-icon" aria-hidden="true"></i>
                    <i data-lucide="graduation-cap" class="hero-decor-icon hero-decor-icon--graduation ui-icon" aria-hidden="true"></i>
                </div>

                <div id="hero-floating-cards" aria-hidden="true">
                    @foreach ($heroCourses as $course)
                        <x-home.hero-float-card
                            :course="$course"
                        />
                    @endforeach
                </div>

                <div class="page-container">
                    <h1 id="home-title">
                        <span class="hero-title-line">ทุกวิชามีเรื่องราว</span><br>
                        <span class="hero-title-line gradient-text">ก่อนคุณลงเรียน</span>
                    </h1>
                    <p>รวมคะแนนและรีวิวรายวิชาจากผู้เรียนจริง เพื่อให้คุณเลือกวิชาได้ชัดเจนขึ้น</p>
                </div>
            </header>

            <section>
                <div class="page-container">
                    <p id="home-statement" data-home-statement aria-label="{{ $homeCopy['statement'] ?? '' }}"></p>
                </div>
            </section>

            <section id="courses" class="home-courses-section">
                <div class="page-container">
                    <div class="popular-courses-heading">
                        <h2>วิชายอดนิยม<br><span class="gradient-text">ที่นักศึกษาสนใจ</span></h2>
                        <a class="pill-button pill-button--dark" href="{{ route('courses.index') }}">
                            ค้นหาวิชาทั้งหมด
                            <i data-lucide="arrow-up-right" class="ui-icon size-4" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="popular-course-rail" id="popular-course-rail" data-home-course-rail>
                    @forelse ($popularCourses as $course)
                        <x-course.card :course="$course" />
                    @empty
                        <p class="py-8 text-sm text-slate-600">ยังไม่มีรายวิชาที่เผยแพร่</p>
                    @endforelse
                </div>

                <div class="page-container popular-course-controls">
                    <div class="popular-course-arrows">
                        <button type="button" data-home-rail-prev aria-label="รายวิชาก่อนหน้า">
                            <i data-lucide="arrow-left" class="ui-icon size-5" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-home-rail-next aria-label="รายวิชาถัดไป">
                            <i data-lucide="arrow-right" class="ui-icon size-5" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </section>

            <section id="features" class="home-highlight-section">
                <div class="page-container">
                    <h2>ไฮไลต์<br><span class="gradient-text">ของ CPRATING</span></h2>
                </div>
                <div class="highlight-rail" id="home-highlight-rail" data-home-highlight-rail role="region" aria-label="ไฮไลต์ของ CPRATING" tabindex="0">
                    <article class="highlight-card rating-highlight-card" data-home-highlight>
                        <p class="highlight-description">
                            คะแนน <span class="gradient-text">rating คิดจากความน่าสนใจของรายวิชา</span>
                            ส่วนปริมาณงานและความยากแสดงเป็นระดับ น้อย–มาก ไม่ปนกับดาว
                        </p>

                        <div class="highlight-panel rating-highlight-panel">
                            <div class="rating-highlight-score">
                                <div class="rating-highlight-score-copy">
                                    <span class="rating-highlight-score-label">ความน่าสนใจของ<br>รายวิชา</span>
                                    @if ($highlight->rating_count)
                                        <b data-count="{{ round($highlight->rating, 1) }}" data-count-start="0.0">0.0</b>
                                        <span class="rating-highlight-stars" aria-label="คะแนนเต็ม 5">★★★★★</span>
                                        <small>จาก {{ number_format($highlight->rating_count) }} คะแนน</small>
                                    @else
                                        <b>—</b>
                                        <small>ยังไม่มีผู้ให้คะแนน</small>
                                    @endif
                                </div>
                            </div>

                            <div class="rating-highlight-metrics">
                                <div class="rating-highlight-metric">
                                    <div class="rating-highlight-metric-heading">
                                        <span>ปริมาณงาน</span>
                                        <strong>{{ $highlight->rating_count ? round($highlight->workload, 1).' / 5' : 'ยังไม่มีข้อมูล' }}</strong>
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
                                            data-highlight-progress="{{ $highlight->rating_count ? round($highlight->workload * 20) : 0 }}"
                                            style="--progress-target: {{ $highlight->rating_count ? round($highlight->workload * 20) : 0 }}%;"
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
                                        <strong>{{ $highlight->rating_count ? round($highlight->difficulty, 1).' / 5' : 'ยังไม่มีข้อมูล' }}</strong>
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
                                            data-highlight-progress="{{ $highlight->rating_count ? round($highlight->difficulty * 20) : 0 }}"
                                            style="--progress-target: {{ $highlight->rating_count ? round($highlight->difficulty * 20) : 0 }}%;"
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

                    <article class="highlight-card review-highlight-card" data-home-highlight>
                        <img src="{{ asset('images/photo-6.jpg') }}" alt="">
                        <p class="highlight-description">รีวิวจากผู้เรียนจริง <span class="highlight-accent">ยืนยันตัวตนด้วยอีเมลมหาวิทยาลัย</span> แต่ไม่เปิดเผยชื่อ จึงเขียนได้ตรงไปตรงมา</p>
                        <div class="review-highlight-quote">
                            @if ($reviews->isNotEmpty())
                                <div class="review-author"><i>ผ</i>ผู้เรียน · {{ $reviews->first()->course?->course_name_th }}</div>
                                <p>{{ $reviews->first()->content }}</p>
                            @else
                                <p>ยังไม่มีความคิดเห็นจากผู้เรียน</p>
                            @endif
                        </div>
                    </article>

                    @if ($curriculumPlan)
                        <article class="highlight-card curriculum-highlight-card" data-home-highlight>
                            <p class="highlight-description curriculum-highlight-title">
                                <span>วิชาที่ต้องเรียน</span>ของแต่ละสาขา<br>
                                ตั้งแต่ปี 1 ถึงปี 4
                            </p>

                            <div class="curriculum-highlight-panel">
                                <div class="curriculum-highlight-heading">
                                    <div>
                                        <small>สาขา</small>
                                        <strong>{{ $curriculumPlan->name }}</strong>
                                    </div>
                                    <a href="{{ route('plans.index') }}">
                                        ดูทุกสาขา
                                        <i data-lucide="arrow-right" class="ui-icon size-4" aria-hidden="true"></i>
                                    </a>
                                </div>

                                <div class="curriculum-highlight-years">
                                    @foreach ($curriculumPlan->courses->groupBy('year') as $year => $yearCourses)
                                        <div class="curriculum-highlight-year">
                                            <span class="curriculum-highlight-year-label">ปี {{ $year }}</span>
                                            <div class="curriculum-highlight-course-list">
                                                @foreach ($yearCourses->take(3) as $planCourse)
                                                    <span>{{ $planCourse->course->course_name_th }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endif
                </div>
                <div class="page-container highlight-controls">
                    <button class="highlight-control-button" type="button" data-home-highlight-toggle aria-label="หยุดเล่นอัตโนมัติ">
                        <i data-lucide="pause" class="ui-icon size-4" aria-hidden="true"></i>
                    </button>
                    <button class="highlight-control-button" type="button" data-home-highlight-prev aria-label="ไฮไลต์ก่อนหน้า">
                        <i data-lucide="arrow-left" class="ui-icon size-4" aria-hidden="true"></i>
                    </button>
                    <button class="highlight-control-button" type="button" data-home-highlight-next aria-label="ไฮไลต์ถัดไป">
                        <i data-lucide="arrow-right" class="ui-icon size-4" aria-hidden="true"></i>
                    </button>
                </div>
            </section>

            <section id="steps">
                <div class="page-container">
                    <h2 class="steps-heading">ใช้งานง่าย<br><span class="gradient-text">ใน 3 ขั้นตอน</span></h2>
                    <div class="steps-grid">
                        <figure>
                            <div class="step-preview-tile" aria-hidden="true">
                                <div class="step-preview-ui">
                                    <div class="search-preview-field">
                                        <i data-lucide="search" class="ui-icon size-4" aria-hidden="true"></i>
                                        <span>CP422301</span>
                                        <i class="search-preview-caret"></i>
                                    </div>
                                    <div class="search-preview-result"><span><b>CP422301</b> ความมั่นคงปลอดภัยไซเบอร์</span><b>★ 4.6</b></div>
                                    <div class="search-preview-result"><span><b>CP422305</b> การเข้ารหัสลับเบื้องต้น</span><b>★ 4.3</b></div>
                                </div>
                            </div>
                            <figcaption>
                                <p><small>ขั้นที่ 1</small><b>ค้นหาวิชาได้ทันที</b> พิมพ์รหัสหรือชื่อวิชา แล้วกรองตามหมวดที่สนใจ ไม่ต้องไล่ดูทีละหน้า</p>
                            </figcaption>
                        </figure>
                        <figure>
                            <div class="step-preview-tile" aria-hidden="true">
                                <div class="step-preview-ui">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex min-w-0 items-center gap-2.5">
                                            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-[#f8c4a5] text-sm font-semibold text-[#6b3f32]">ม</span>
                                            <span class="truncate text-sm font-semibold text-slate-900">มินท์</span>
                                        </div>
                                        <span class="shrink-0 text-sm tracking-normal text-orange-400">★★★★<span class="text-slate-300">★</span></span>
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
                            <figcaption>
                                <p><small>ขั้นที่ 2</small><b>อ่านคะแนนและรีวิวจริง</b> เปรียบเทียบเนื้อหา ภาระงาน และความยากจากคนที่เคยเรียนมาแล้ว</p>
                            </figcaption>
                        </figure>
                        <figure>
                            <div class="step-preview-tile" aria-hidden="true">
                                <div class="step-preview-ui">
                                    <div class="step-preview-meta step-plan-meta"><b>เทอม 1 ปี 3</b><b>9 / 18 หน่วยกิต</b></div>
                                    <div class="step-plan-course-row"><span>CP422301 ความมั่นคงปลอดภัยไซเบอร์</span><b>3</b></div>
                                    <div class="step-plan-course-row"><span>CP354101 ปัญญาประดิษฐ์</span><b>3</b></div>
                                    <div class="step-plan-course-row"><span>CP352005 เครือข่ายคอมพิวเตอร์</span><b>3</b></div>
                                </div>
                            </div>
                            <figcaption>
                                <p><small>ขั้นที่ 3</small><b>เลือกสาขา แล้วจัดแผนการเรียน</b> เลือกหลักสูตรของสาขาที่เรียน ดูหน่วยกิตรวม แล้วลงทะเบียนอย่างมั่นใจ</p>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </section>

            <section id="reviews">
                <div class="page-container">
                    <div class="reviews-heading">
                        <h2>เสียงจาก<br><span class="gradient-text">คนที่เคยเรียน</span></h2>
                        <p>ตัวอย่างการแสดงผลความคิดเห็นและคะแนนรายวิชา เพื่อให้เห็นว่าข้อมูลจากผู้เรียนจะช่วยตัดสินใจก่อนลงทะเบียนอย่างไร</p>
                    </div>
                    <div class="review-grid">
                        <x-home.review-showcase />
                    </div>
                    <div class="reviews-more">
                        <a class="pill-button pill-button--dark" href="{{ route('courses.index') }}">
                            ดูรีวิวทั้งหมด
                            <i data-lucide="arrow-up-right" class="ui-icon size-4" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </section>

            <section id="trust">
                <div class="page-container trust-wrapper">
                    <div>
                        <p class="section-eyebrow">หลักการของเรา</p>
                        <h2>รีวิวที่<span class="gradient-text">เชื่อถือได้</span><br>มาจากผู้เรียนจริง</h2>
                        <p class="page-lead">CPRATING รวมคะแนนและรีวิวรายวิชา ให้คุณเปรียบเทียบเนื้อหา ภาระงาน และความยาก ก่อนตัดสินใจลงทะเบียน</p>
                        <a class="trust-link" href="{{ route('courses.index') }}">
                            เริ่มสำรวจรายวิชา
                            <i data-lucide="arrow-up-right" class="ui-icon size-4" aria-hidden="true"></i>
                        </a>
                    </div>
                    <ul class="trust-points">
                        <li>
                            <i class="trust-point-icon"><i data-lucide="graduation-cap" class="ui-icon size-6" aria-hidden="true"></i></i>
                            <div><b>ยืนยันด้วยอีเมลมหาวิทยาลัย</b><span>ทุกรีวิวมาจากบัญชีที่ยืนยันตัวตนแล้ว</span></div>
                        </li>
                        <li>
                            <i class="trust-point-icon"><i data-lucide="star" class="ui-icon size-6" aria-hidden="true"></i></i>
                            <div><b>คะแนนแยกตามหัวข้อ</b><span>ไม่สรุปเป็นดาวรวมค่าเดียว</span></div>
                        </li>
                        <li>
                            <i class="trust-point-icon"><i data-lucide="eye-off" class="ui-icon size-6" aria-hidden="true"></i></i>
                            <div><b>ไม่เปิดเผยชื่อผู้รีวิว</b><span>เขียนได้ตรงไปตรงมา ไม่ต้องกังวล</span></div>
                        </li>
                    </ul>
                </div>
            </section>

            <section id="faq" class="home-faq-section">
                <div class="page-container centered-section">
                    <h2>คำถามที่<span class="gradient-text">พบบ่อย</span></h2>
                    <div class="faq-list faq-align-left">
                        <details>
                            <summary>ใครเขียนความคิดเห็นได้บ้าง</summary>
                            <div class="faq-answer"><p>ผู้ใช้ที่สมัครด้วยอีเมลมหาวิทยาลัยและเข้าสู่ระบบแล้ว</p></div>
                        </details>
                        <details>
                            <summary>ชื่อผู้เขียนความคิดเห็นจะถูกเปิดเผยไหม</summary>
                            <div class="faq-answer"><p>หน้ารีวิวแสดงเป็นผู้เรียน โดยไม่แสดงชื่อบัญชี</p></div>
                        </details>
                        <details>
                            <summary>คะแนนรายวิชาคำนวณอย่างไร</summary>
                            <div class="faq-answer"><p>คะแนนดาวมาจากค่าเฉลี่ยความน่าสนใจ ส่วนความยากและปริมาณงานแสดงแยกกันจากผู้ให้คะแนน</p></div>
                        </details>
                        <details>
                            <summary>ข้อมูลแผนการเรียนมาจากไหน</summary>
                            <div class="faq-answer"><p>แสดงเฉพาะหลักสูตรและรายวิชาที่เผยแพร่ในระบบ</p></div>
                        </details>
                        <details>
                            <summary>ใช้งานฟรีหรือไม่</summary>
                            <div class="faq-answer"><p>ค้นหาวิชา อ่านความคิดเห็น และดูแผนการเรียนได้ฟรี การให้คะแนนและเขียนความคิดเห็นต้องเข้าสู่ระบบ</p></div>
                        </details>
                    </div>
                </div>
            </section>

        </main>
    @endif

    <script type="application/json" data-home-copy>@json($homeCopy)</script>
    <div id="scroll-progress" aria-hidden="true"><i></i></div>
    <div id="toast" role="status" aria-live="polite"></div>
    <button id="back-to-top" type="button" aria-label="กลับขึ้นบน"><i data-lucide="arrow-up" class="ui-icon size-5" aria-hidden="true"></i></button>

    <x-footer />
@endsection
